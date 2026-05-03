<?php

namespace App\Themes\Services;

use App\Core\Tenancy\Context\WebsiteContext;
use App\Core\Website\Models\Website;
use App\Themes\Registries\ThemeRegistry;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;

class ThemeManager
{
    protected ?array $activeTheme = null;

    public function __construct(
        protected ThemeRegistry $themes,
        protected WebsiteContext $context
    ) {
    }

    public function activateFor(Website $website): ?array
    {
        $this->context->set($website);

        $theme = $this->themes->find($website->theme_slug ?: config('cms.default_theme'))
            ?? $this->themes->find(config('cms.default_theme'));

        if (!$theme) {
            return null;
        }

        $this->activeTheme = $theme;

        View::addNamespace($theme['view_namespace'], $theme['path'].'/resources/views');
        View::share('activeTheme', $theme);
        View::share('activeWebsite', $website);

        return $theme;
    }

    public function bootActiveTheme(): void
    {
        $website = $this->context->get();

        if ($website) {
            $this->activateFor($website);
        }
    }

    public function active(): ?array
    {
        return $this->activeTheme;
    }

    public function themeSettings(Website $website, string $slug): array
    {
        return cache()->remember(
            "theme-settings:{$website->id}:{$slug}",
            now()->addMinutes(30),
            fn () => $website->siteSettings()
                ->where('group_name', "theme:{$slug}")
                ->pluck('value', 'key_name')
                ->map(fn ($value) => is_array($value) && array_key_exists('value', $value) ? $value['value'] : $value)
                ->toArray()
        );
    }

    public function view(string $template, array $data = []): ViewContract
    {
        $theme = $this->activeTheme;

        abort_if(!$theme, 500, 'No active theme is configured.');

        return view($theme['view_namespace'].'::'.$template, $data);
    }
}
