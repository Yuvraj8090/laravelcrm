<?php

namespace Database\Seeders;

use App\Cms\Content\Models\Taxonomy;
use App\Core\Website\Models\Website;
use Illuminate\Database\Seeder;

class TaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $website = Website::query()->firstOrFail();

        foreach ([
            ['type' => 'category', 'name' => 'News', 'slug' => 'news'],
            ['type' => 'category', 'name' => 'Updates', 'slug' => 'updates'],
            ['type' => 'tag', 'name' => 'Laravel', 'slug' => 'laravel'],
            ['type' => 'tag', 'name' => 'CMS', 'slug' => 'cms'],
        ] as $taxonomy) {
            Taxonomy::withoutGlobalScopes()->updateOrCreate(
                ['website_id' => $website->id, 'slug' => $taxonomy['slug'], 'type' => $taxonomy['type']],
                [...$taxonomy, 'website_id' => $website->id]
            );
        }
    }
}
