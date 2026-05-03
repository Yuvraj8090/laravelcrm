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
use App\Http\Controllers\Crm\AuthController as CrmAuthController;
use App\Http\Controllers\Crm\CommunicationController;
use App\Http\Controllers\Crm\CompanyController;
use App\Http\Controllers\Crm\ContactController;
use App\Http\Controllers\Crm\DashboardController as CrmDashboardController;
use App\Http\Controllers\Crm\DealController;
use App\Http\Controllers\Crm\InvoiceController;
use App\Http\Controllers\Crm\LeadController;
use App\Http\Controllers\Crm\PipelineController;
use App\Http\Controllers\Crm\QuoteController;
use App\Http\Controllers\Crm\SettingsController;
use App\Http\Controllers\Crm\TaskController;
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

Route::prefix('crm')->name('crm.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [CrmAuthController::class, 'create'])->name('login');
        Route::post('/login', [CrmAuthController::class, 'store'])->name('login.store');
    });

    Route::middleware(['auth', 'crm.role:admin,manager,sales_rep'])->group(function () {
        Route::post('/logout', [CrmAuthController::class, 'destroy'])->name('logout');
        Route::get('/dashboard', CrmDashboardController::class)->name('dashboard');

        Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::post('/companies', [CompanyController::class, 'store'])->middleware('crm.role:admin,manager')->name('companies.store');
        Route::put('/companies/{company}', [CompanyController::class, 'update'])->middleware('crm.role:admin,manager')->name('companies.update');
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->middleware('crm.role:admin')->name('companies.destroy');

        Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
        Route::post('/contacts', [ContactController::class, 'store'])->middleware('crm.role:admin,manager,sales_rep')->name('contacts.store');
        Route::put('/contacts/{contact}', [ContactController::class, 'update'])->middleware('crm.role:admin,manager,sales_rep')->name('contacts.update');
        Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->middleware('crm.role:admin,manager')->name('contacts.destroy');

        Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
        Route::post('/leads', [LeadController::class, 'store'])->middleware('crm.role:admin,manager,sales_rep')->name('leads.store');
        Route::put('/leads/{lead}', [LeadController::class, 'update'])->middleware('crm.role:admin,manager,sales_rep')->name('leads.update');
        Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->middleware('crm.role:admin,manager')->name('leads.destroy');

        Route::get('/deals', [DealController::class, 'index'])->name('deals.index');
        Route::post('/deals', [DealController::class, 'store'])->middleware('crm.role:admin,manager,sales_rep')->name('deals.store');
        Route::put('/deals/{deal}', [DealController::class, 'update'])->middleware('crm.role:admin,manager,sales_rep')->name('deals.update');
        Route::delete('/deals/{deal}', [DealController::class, 'destroy'])->middleware('crm.role:admin,manager')->name('deals.destroy');

        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/tasks', [TaskController::class, 'store'])->middleware('crm.role:admin,manager,sales_rep')->name('tasks.store');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->middleware('crm.role:admin,manager,sales_rep')->name('tasks.update');
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->middleware('crm.role:admin,manager')->name('tasks.destroy');

        Route::get('/communications', [CommunicationController::class, 'index'])->name('communications.index');
        Route::post('/communications', [CommunicationController::class, 'store'])->middleware('crm.role:admin,manager,sales_rep')->name('communications.store');
        Route::put('/communications/{communication}', [CommunicationController::class, 'update'])->middleware('crm.role:admin,manager,sales_rep')->name('communications.update');
        Route::delete('/communications/{communication}', [CommunicationController::class, 'destroy'])->middleware('crm.role:admin,manager')->name('communications.destroy');

        Route::get('/pipelines', [PipelineController::class, 'index'])->middleware('crm.role:admin,manager')->name('pipelines.index');
        Route::post('/pipelines', [PipelineController::class, 'store'])->middleware('crm.role:admin,manager')->name('pipelines.store');
        Route::put('/pipelines/{pipeline}', [PipelineController::class, 'update'])->middleware('crm.role:admin,manager')->name('pipelines.update');
        Route::delete('/pipelines/{pipeline}', [PipelineController::class, 'destroy'])->middleware('crm.role:admin')->name('pipelines.destroy');

        Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
        Route::post('/quotes', [QuoteController::class, 'store'])->middleware('crm.role:admin,manager,sales_rep')->name('quotes.store');
        Route::put('/quotes/{quote}', [QuoteController::class, 'update'])->middleware('crm.role:admin,manager,sales_rep')->name('quotes.update');
        Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy'])->middleware('crm.role:admin,manager')->name('quotes.destroy');

        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('/invoices', [InvoiceController::class, 'store'])->middleware('crm.role:admin,manager')->name('invoices.store');
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->middleware('crm.role:admin,manager')->name('invoices.update');
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->middleware('crm.role:admin')->name('invoices.destroy');

        Route::get('/settings/theme', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings/theme', [SettingsController::class, 'update'])->name('settings.update');
    });
});
