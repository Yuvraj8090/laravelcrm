<?php

namespace Database\Seeders;

use App\Core\Website\Models\Website;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $website = Website::query()->updateOrCreate(
            ['slug' => 'starter-site'],
            [
                'name' => 'Starter Site',
                'status' => 'active',
                'primary_domain' => 'starter.test',
                'theme_slug' => config('cms.default_theme'),
                'locale' => 'en',
                'timezone' => 'UTC',
            ]
        );

        $website->domains()->updateOrCreate(
            ['domain' => 'starter.test'],
            ['is_primary' => true]
        );
    }
}
