<?php

namespace Database\Seeders;

use App\Core\Website\Models\Website;
use App\Core\Website\Models\WebsitePlugin;
use App\Plugins\Registries\PluginRegistry;
use App\Settings\Models\Setting;
use App\Themes\Registries\ThemeRegistry;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $website = Website::query()->firstOrFail();

        foreach (app(ThemeRegistry::class)->all() as $theme) {
            \DB::table('installed_themes')->updateOrInsert(
                ['slug' => $theme['slug']],
                [
                    'name' => $theme['name'],
                    'version' => $theme['version'],
                    'author' => $theme['author'] ?? null,
                    'description' => $theme['description'] ?? null,
                    'path' => $theme['path'],
                    'is_enabled' => true,
                    'manifest' => json_encode($theme),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        foreach (app(PluginRegistry::class)->all() as $plugin) {
            \DB::table('installed_plugins')->updateOrInsert(
                ['slug' => $plugin['slug']],
                [
                    'name' => $plugin['name'],
                    'version' => $plugin['version'],
                    'author' => $plugin['author'] ?? null,
                    'description' => $plugin['description'] ?? null,
                    'path' => $plugin['path'],
                    'is_enabled' => true,
                    'manifest' => json_encode($plugin),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            WebsitePlugin::query()->updateOrCreate(
                ['website_id' => $website->id, 'plugin_slug' => $plugin['slug']],
                ['is_active' => true, 'settings' => ['features' => 'all'], 'activated_at' => now()]
            );
        }

        $settings = [
            ['group_name' => 'theme:starter', 'key_name' => 'colors.primary', 'value' => ['value' => '#0f6b63']],
            ['group_name' => 'theme:starter', 'key_name' => 'branding.logo', 'value' => ['value' => '/logo.svg']],
            ['group_name' => 'seo', 'key_name' => 'default_title', 'value' => ['value' => 'Starter Site']],
            ['group_name' => 'seo', 'key_name' => 'default_description', 'value' => ['value' => 'Starter SEO description']],
            ['group_name' => 'plugin:seo-toolkit', 'key_name' => 'features', 'value' => ['value' => 'sitemap,metadata,schema']],
        ];

        foreach ($settings as $setting) {
            Setting::withoutGlobalScopes()->updateOrCreate(
                [
                    'website_id' => $website->id,
                    'group_name' => $setting['group_name'],
                    'key_name' => $setting['key_name'],
                ],
                ['value' => $setting['value'], 'autoload' => true]
            );
        }
    }
}
