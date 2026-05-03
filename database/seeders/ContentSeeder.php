<?php

namespace Database\Seeders;

use App\Cms\Content\Models\Content;
use App\Cms\Content\Models\PageSection;
use App\Cms\Content\Models\Taxonomy;
use App\Core\Website\Models\Website;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $website = Website::query()->firstOrFail();
        $author = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $pages = [
            ['title' => 'Home', 'slug' => 'home'],
            ['title' => 'About', 'slug' => 'about'],
            ['title' => 'Contact', 'slug' => 'contact'],
            ['title' => 'Privacy Policy', 'slug' => 'privacy-policy'],
        ];

        foreach ($pages as $index => $page) {
            $content = Content::withoutGlobalScopes()->updateOrCreate(
                ['website_id' => $website->id, 'type' => 'page', 'slug' => $page['slug']],
                [
                    'website_id' => $website->id,
                    'type' => 'page',
                    'status' => 'published',
                    'author_id' => $author->id,
                    'title' => $page['title'],
                    'excerpt' => $page['title'].' excerpt',
                    'body' => $page['title'].' page body.',
                    'sort_order' => $index,
                    'published_at' => now(),
                    'meta_title' => $page['title'].' | Starter Site',
                    'meta_description' => 'Default description for '.$page['title'],
                ]
            );

            PageSection::withoutGlobalScopes()->updateOrCreate(
                ['website_id' => $website->id, 'content_id' => $content->id, 'type' => 'hero', 'sort_order' => 0],
                [
                    'website_id' => $website->id,
                    'name' => $page['title'].' Hero',
                    'settings' => [
                        'headline' => $page['title'],
                        'body' => 'Welcome to the '.$page['title'].' page.',
                        'background' => '#ffffff',
                        'color' => '#1d2b34',
                    ],
                ]
            );
        }

        $postCategory = Taxonomy::withoutGlobalScopes()->where('type', 'category')->where('slug', 'news')->first();
        $tag = Taxonomy::withoutGlobalScopes()->where('type', 'tag')->where('slug', 'laravel')->first();

        foreach (range(1, 3) as $index) {
            $post = Content::withoutGlobalScopes()->updateOrCreate(
                ['website_id' => $website->id, 'type' => 'post', 'slug' => "sample-post-{$index}"],
                [
                    'website_id' => $website->id,
                    'type' => 'post',
                    'status' => 'published',
                    'author_id' => $author->id,
                    'title' => "Sample Post {$index}",
                    'excerpt' => "Sample post {$index} excerpt",
                    'body' => "This is sample post {$index}.",
                    'published_at' => now()->subDays($index),
                    'meta_title' => "Sample Post {$index}",
                    'meta_description' => 'Sample seeded post',
                ]
            );

            $post->taxonomies()->sync(array_filter([$postCategory?->id, $tag?->id]));
        }
    }
}
