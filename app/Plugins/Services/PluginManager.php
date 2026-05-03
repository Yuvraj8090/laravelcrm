<?php

namespace App\Plugins\Services;

use App\Core\Tenancy\Context\WebsiteContext;
use App\Core\Website\Models\Website;
use App\Plugins\Registries\PluginRegistry;

class PluginManager
{
    public function __construct(
        protected PluginRegistry $plugins,
        protected WebsiteContext $context
    ) {
    }

    public function activeForCurrentWebsite(): array
    {
        $website = $this->context->get();

        if (!$website) {
            return [];
        }

        $activeSlugs = $website->plugins()
            ->where('is_active', true)
            ->pluck('plugin_slug')
            ->all();

        return $this->plugins->all()
            ->whereIn('slug', $activeSlugs)
            ->values()
            ->all();
    }

    public function bootActivePlugins(): void
    {
        foreach ($this->activeForCurrentWebsite() as $plugin) {
            cms_do_action('plugin.booting', $plugin);
        }
    }

    public function missingDependencies(string $slug, Website $website): array
    {
        $plugin = $this->plugins->find($slug);

        if (!$plugin) {
            return ['Plugin manifest could not be found.'];
        }

        $required = $plugin['requires_plugins'] ?? [];

        if ($required === []) {
            return [];
        }

        $active = $website->plugins()
            ->where('is_active', true)
            ->pluck('plugin_slug')
            ->all();

        return array_values(array_diff($required, $active));
    }
}
