<?php

use App\Http\Controllers\Admin\BuilderController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PluginController;
use App\Http\Controllers\Admin\TaxonomyController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\WebsiteController;
use App\Http\Controllers\Admin\WidgetController;
use App\Http\Controllers\Auth\SessionController;
use App\Core\Website\Models\Website;
use App\Themes\Services\ThemeManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [SessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/sites/{website:slug}/preview', function (Website $website, ThemeManager $themes) {
    $themes->activateFor($website);

    return $themes->view('pages.home', [
        'website' => $website,
        'pageTitle' => $website->name,
    ]);
})->name('sites.preview');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin,editor,author'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('websites', WebsiteController::class)->except('show');
        Route::post('websites/{website}/restore', [WebsiteController::class, 'restore'])->name('websites.restore');

        Route::get('contents', [ContentController::class, 'index'])->name('contents.index');
        Route::get('contents/create', [ContentController::class, 'create'])->name('contents.create');
        Route::post('contents', [ContentController::class, 'store'])->name('contents.store');
        Route::get('contents/{content}/edit', [ContentController::class, 'edit'])->name('contents.edit');
        Route::put('contents/{content}', [ContentController::class, 'update'])->name('contents.update');
        Route::delete('contents/{content}', [ContentController::class, 'destroy'])->name('contents.destroy');
        Route::post('contents/{content}/restore', [ContentController::class, 'restore'])->name('contents.restore');
        Route::post('contents/bulk', [ContentController::class, 'bulk'])->name('contents.bulk');

        Route::get('contents/{content}/builder', [BuilderController::class, 'edit'])->name('builder.edit');
        Route::put('contents/{content}/builder', [BuilderController::class, 'update'])->name('builder.update');

        Route::get('taxonomies', [TaxonomyController::class, 'index'])->name('taxonomies.index');
        Route::post('taxonomies', [TaxonomyController::class, 'store'])->name('taxonomies.store');
        Route::put('taxonomies/{taxonomy}', [TaxonomyController::class, 'update'])->name('taxonomies.update');
        Route::delete('taxonomies/{taxonomy}', [TaxonomyController::class, 'destroy'])->name('taxonomies.destroy');

        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

        Route::get('themes', [ThemeController::class, 'index'])->name('themes.index');
        Route::post('themes/{slug}/activate', [ThemeController::class, 'activate'])->name('themes.activate');
        Route::get('themes/{slug}/preview', [ThemeController::class, 'preview'])->name('themes.preview');
        Route::get('themes/{slug}/settings', [ThemeController::class, 'settings'])->name('themes.settings');
        Route::put('themes/{slug}/settings', [ThemeController::class, 'updateSettings'])->name('themes.settings.update');

        Route::get('plugins', [PluginController::class, 'index'])->name('plugins.index');
        Route::post('plugins/{slug}/activate', [PluginController::class, 'activate'])->name('plugins.activate');
        Route::post('plugins/{slug}/deactivate', [PluginController::class, 'deactivate'])->name('plugins.deactivate');
        Route::get('plugins/{slug}/settings', [PluginController::class, 'settings'])->name('plugins.settings');
        Route::put('plugins/{slug}/settings', [PluginController::class, 'updateSettings'])->name('plugins.settings.update');

        Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
        Route::post('menus', [MenuController::class, 'store'])->name('menus.store');
        Route::put('menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');

        Route::get('widgets', [WidgetController::class, 'index'])->name('widgets.index');
        Route::post('widgets', [WidgetController::class, 'store'])->name('widgets.store');
        Route::put('widgets/{widget}', [WidgetController::class, 'update'])->name('widgets.update');
        Route::delete('widgets/{widget}', [WidgetController::class, 'destroy'])->name('widgets.destroy');
    });
