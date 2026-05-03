<?php

namespace App\Providers;

use App\Core\Support\Hooks\HookManager;
use App\Core\Tenancy\Context\WebsiteContext;
use App\Plugins\Registries\PluginRegistry;
use App\Plugins\Services\PluginManager;
use App\Support\Services\ActivityLogger;
use App\Themes\Registries\ThemeRegistry;
use App\Themes\Services\ThemeManager;
use Illuminate\Support\ServiceProvider;

class CmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WebsiteContext::class, fn () => new WebsiteContext());
        $this->app->singleton(HookManager::class, fn () => new HookManager());
        $this->app->singleton(ThemeRegistry::class, fn () => new ThemeRegistry(config('cms.themes_path')));
        $this->app->singleton(PluginRegistry::class, fn () => new PluginRegistry(config('cms.plugins_path')));

        $this->app->singleton(ThemeManager::class, function ($app) {
            return new ThemeManager(
                $app->make(ThemeRegistry::class),
                $app->make(WebsiteContext::class),
            );
        });

        $this->app->singleton(PluginManager::class, function ($app) {
            return new PluginManager(
                $app->make(PluginRegistry::class),
                $app->make(WebsiteContext::class),
            );
        });
        $this->app->singleton(ActivityLogger::class, fn ($app) => new ActivityLogger(
            $app->make(WebsiteContext::class)
        ));
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $this->app->booted(function (): void {
            $this->app->make(ThemeManager::class)->bootActiveTheme();
            $this->app->make(PluginManager::class)->bootActivePlugins();
        });
    }
}
