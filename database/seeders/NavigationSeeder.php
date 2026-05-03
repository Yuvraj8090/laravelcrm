<?php

namespace Database\Seeders;

use App\Cms\Content\Models\Menu;
use App\Cms\Content\Models\Widget;
use App\Core\Website\Models\Website;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        $website = Website::query()->firstOrFail();

        $menu = Menu::withoutGlobalScopes()->updateOrCreate(
            ['website_id' => $website->id, 'location' => 'header'],
            ['name' => 'Main Navigation']
        );

        $menu->items()->delete();
        foreach ([
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'About', 'url' => '/about'],
            ['title' => 'Contact', 'url' => '/contact'],
        ] as $index => $item) {
            $menu->items()->create([...$item, 'sort_order' => $index]);
        }

        Widget::withoutGlobalScopes()->updateOrCreate(
            ['website_id' => $website->id, 'name' => 'Footer About'],
            [
                'area' => 'footer',
                'type' => 'text',
                'settings' => ['title' => 'About This Site', 'body' => 'A seeded CMS widget.'],
                'is_active' => true,
            ]
        );
    }
}
