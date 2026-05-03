<?php

namespace App\Plugins\Services;

use App\Core\Tenancy\Context\WebsiteContext;
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
}
