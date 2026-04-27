<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CarShowController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Manage\CarAttributeCategoryController;
use App\Http\Controllers\Manage\CarAttributeController;
use App\Http\Controllers\Manage\CarAttributeValueController;
use App\Http\Controllers\Manage\CarController;
use App\Http\Controllers\Manage\FaqController;
use App\Http\Controllers\Manage\HomePartnerController;
use App\Http\Controllers\Manage\HomeServiceTileController;
use App\Http\Controllers\Manage\HomeTestimonialController;
use App\Http\Controllers\Manage\MessageController;
use App\Http\Controllers\Manage\PageCategoryController;
use App\Http\Controllers\Manage\PageController;
use App\Http\Controllers\Manage\ReferenceController;
use App\Http\Controllers\Manage\ReportController;
use App\Http\Controllers\Manage\SliderController;
use App\Http\Controllers\Manage\CarDownPaymentController;
use App\Http\Controllers\Manage\CarDurationController;
use App\Http\Controllers\Manage\CarExtraServiceController;
use App\Http\Controllers\Manage\CarKilometerOptionController;
use App\Http\Controllers\Manage\CarPackageController;
use App\Http\Controllers\Manage\CarPriceMatrixController;
use App\Http\Controllers\Manage\RentalRequestController;
use App\Http\Controllers\Manage\UserController;
use App\Http\Controllers\Manage\WeCallYouController as ManageWeCallYouController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WeCallYouController;
use Illuminate\Support\Facades\Route;

Route::prefix('manage')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth')->group(function (): void {
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    });

    Route::middleware(['auth', 'manage'])->group(function (): void {
        Route::get('/', [ManageController::class, 'index'])->name('dashboard');
        Route::get('/settings', [ManageController::class, 'settings'])->name('settings');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('/cache/clear', [ManageController::class, 'clearCache'])->name('cache.clear');
        Route::get('/menus', [ManageController::class, 'menus'])->name('menus.index');
        Route::post('/menus', [SettingsController::class, 'updateMenus'])->name('menus.update');

        Route::resource('users', UserController::class)->except(['show']);

        Route::resource('cars', CarController::class)->except(['show']);
        Route::post('cars/{car}/attribute-pivots', [CarController::class, 'storeAttributePivot'])
            ->name('cars.attribute-pivots.store');
        Route::delete('cars/{car}/attribute-pivots/{car_attribute_pivot}', [CarController::class, 'destroyAttributePivot'])
            ->name('cars.attribute-pivots.destroy');

        Route::resource('cars.price-matrices', CarPriceMatrixController::class)
            ->shallow()
            ->except(['index', 'show']);

        Route::resource('car-down-payments', CarDownPaymentController::class)->except(['show']);
        Route::resource('car-packages', CarPackageController::class)->except(['show']);
        Route::resource('car-durations', CarDurationController::class)->except(['show']);
        Route::resource('car-kilometer-options', CarKilometerOptionController::class)->except(['show']);
        Route::resource('car-extra-services', CarExtraServiceController::class)->except(['show']);
        Route::resource('car-attribute-categories', CarAttributeCategoryController::class)->except(['show']);
        Route::resource('car-attributes', CarAttributeController::class)->except(['show']);
        Route::resource('car-attribute-values', CarAttributeValueController::class)->except(['show']);

        Route::resource('rental-requests', RentalRequestController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::resource('messages', MessageController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::resource('we-call-you', ManageWeCallYouController::class)
            ->only(['index', 'show', 'update', 'destroy'])
            ->parameters(['we-call-you' => 'we_call_you']);
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export/{format}', [ReportController::class, 'export'])
            ->whereIn('format', ['csv', 'excel', 'pdf'])
            ->name('reports.export');

        Route::resource('sliders', SliderController::class)->except(['show']);
        Route::post('sliders/reorder', [SliderController::class, 'reorder'])->name('sliders.reorder');
        Route::resource('home-service-tiles', HomeServiceTileController::class)->except(['show']);
        Route::post('home-service-tiles/reorder', [HomeServiceTileController::class, 'reorder'])->name('home-service-tiles.reorder');
        Route::resource('home-partners', HomePartnerController::class)->except(['show']);
        Route::post('home-partners/reorder', [HomePartnerController::class, 'reorder'])->name('home-partners.reorder');
        Route::resource('home-testimonials', HomeTestimonialController::class)->except(['show']);
        Route::post('home-testimonials/reorder', [HomeTestimonialController::class, 'reorder'])->name('home-testimonials.reorder');
        Route::resource('references', ReferenceController::class)->except(['show']);
        Route::resource('page-categories', PageCategoryController::class)->except(['show']);
        Route::resource('pages', PageController::class)->except(['show']);
        Route::resource('faqs', FaqController::class)->except(['show']);
        Route::post('faqs/reorder', [FaqController::class, 'reorder'])->name('faqs.reorder');
    });
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/uzun-donem-arac-kiralama/{slug}', [CarShowController::class, 'show'])->name('cars.show');
Route::get('/favoriler', [FavoriteController::class, 'index'])->name('favorites.index');
Route::post('/favoriler/{car:slug}', [FavoriteController::class, 'store'])->name('favorites.store');
Route::delete('/favoriler/{car:slug}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
Route::get('/biz-sizi-arayalim', [WeCallYouController::class, 'create'])->name('we-call-you.create');
Route::post('/biz-sizi-arayalim', [WeCallYouController::class, 'store'])->name('we-call-you.store');
