<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SaveThemeSettingsRequest;
use App\Settings\Models\Setting;
use App\Support\Services\ActivityLogger;
use App\Themes\Registries\ThemeRegistry;
use App\Themes\Services\ThemeManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThemeController extends AdminController
{
    public function __construct(
        protected ThemeRegistry $themes,
        protected ThemeManager $themeManager,
        protected ActivityLogger $activity
    ) {
    }

    public function index(Request $request): View
    {
        $website = $this->currentWebsite($request);
        $themes = $this->themes->all();

        return view('admin.themes.index', compact('website', 'themes'));
    }

    public function activate(Request $request, string $slug): RedirectResponse
    {
        $website = $this->currentWebsite($request);
        abort_unless($this->themes->find($slug), 404);

        $website->update(['theme_slug' => $slug]);
        cache()->forget("theme-settings:{$website->id}:{$slug}");
        $this->activity->log('theme.activated', $website, ['theme' => $slug]);

        return back()->with('status', 'Theme activated.');
    }

    public function preview(Request $request, string $slug)
    {
        $website = $this->currentWebsite($request);
        $theme = $this->themes->find($slug);
        abort_unless($theme, 404);

        $this->themeManager->activateFor($website->fill(['theme_slug' => $slug]));

        return $this->themeManager->view('pages.home', [
            'website' => $website,
            'pageTitle' => "{$website->name} preview",
        ]);
    }

    public function settings(Request $request, string $slug): View
    {
        $website = $this->currentWebsite($request);
        abort_unless($this->themes->find($slug), 404);
        $settings = $this->themeManager->themeSettings($website, $slug);

        return view('admin.themes.settings', compact('website', 'slug', 'settings'));
    }

    public function updateSettings(SaveThemeSettingsRequest $request, string $slug): RedirectResponse
    {
        $website = $this->currentWebsite($request);

        foreach ($request->validated('settings') as $key => $value) {
            Setting::query()->updateOrCreate(
                [
                    'website_id' => $website->id,
                    'group_name' => "theme:{$slug}",
                    'key_name' => $key,
                ],
                ['value' => ['value' => $value], 'autoload' => true]
            );
        }

        cache()->forget("theme-settings:{$website->id}:{$slug}");
        $this->activity->log('theme.settings.updated', $website, ['theme' => $slug]);

        return back()->with('status', 'Theme settings saved.');
    }
}
