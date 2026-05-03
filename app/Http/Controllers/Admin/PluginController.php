<?php

namespace App\Http\Controllers\Admin;

use App\Core\Website\Models\WebsitePlugin;
use App\Http\Requests\SavePluginSettingsRequest;
use App\Plugins\Registries\PluginRegistry;
use App\Plugins\Services\PluginManager;
use App\Support\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PluginController extends AdminController
{
    public function __construct(
        protected PluginRegistry $plugins,
        protected PluginManager $pluginManager,
        protected ActivityLogger $activity
    ) {
    }

    public function index(Request $request): View
    {
        $website = $this->currentWebsite($request);
        $plugins = $this->plugins->all();
        $active = $website->plugins()->pluck('is_active', 'plugin_slug');

        return view('admin.plugins.index', compact('website', 'plugins', 'active'));
    }

    public function activate(Request $request, string $slug): RedirectResponse
    {
        $website = $this->currentWebsite($request);
        $missing = $this->pluginManager->missingDependencies($slug, $website);

        if ($missing !== []) {
            return back()->withErrors(['plugin' => 'Missing dependencies: '.implode(', ', $missing)]);
        }

        WebsitePlugin::query()->updateOrCreate(
            ['website_id' => $website->id, 'plugin_slug' => $slug],
            ['is_active' => true, 'activated_at' => now()]
        );

        $this->activity->log('plugin.activated', $website, ['plugin' => $slug]);

        return back()->with('status', 'Plugin activated.');
    }

    public function deactivate(Request $request, string $slug): RedirectResponse
    {
        $website = $this->currentWebsite($request);
        $website->plugins()->where('plugin_slug', $slug)->update(['is_active' => false]);
        $this->activity->log('plugin.deactivated', $website, ['plugin' => $slug]);

        return back()->with('status', 'Plugin deactivated.');
    }

    public function settings(Request $request, string $slug): View
    {
        $website = $this->currentWebsite($request);
        $plugin = $this->plugins->find($slug);
        abort_unless($plugin, 404);

        $settings = $website->plugins()->where('plugin_slug', $slug)->value('settings') ?? [];

        return view('admin.plugins.settings', compact('website', 'plugin', 'settings'));
    }

    public function updateSettings(SavePluginSettingsRequest $request, string $slug): RedirectResponse
    {
        $website = $this->currentWebsite($request);
        $website->plugins()->updateOrCreate(
            ['plugin_slug' => $slug],
            [
                'is_active' => true,
                'settings' => $request->validated('settings'),
                'activated_at' => now(),
            ]
        );

        $this->activity->log('plugin.settings.updated', $website, ['plugin' => $slug]);

        return back()->with('status', 'Plugin settings saved.');
    }
}
