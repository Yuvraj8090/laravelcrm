<?php

use App\Core\Website\Models\Website;
use App\Themes\Services\ThemeManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('platform', [
        'title' => 'Laravel CMS Platform',
        'documents' => [
            'Architecture blueprint' => 'docs/laravel-multitenant-cms-blueprint.md',
            'Platform roadmap' => 'docs/wordpress-wix-unified-platform-roadmap.md',
        ],
    ]);
});

Route::get('/sites/{website:slug}/preview', function (Website $website, ThemeManager $themes) {
    $themes->activateFor($website);

    return $themes->view('pages.home', [
        'website' => $website,
        'pageTitle' => $website->name,
    ]);
});
