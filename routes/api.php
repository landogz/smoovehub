<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\SellerRegistrationController;
use App\Http\Controllers\API\Catalog\CatalogController;
use App\Http\Controllers\API\Install\InstallController;
use App\Http\Controllers\API\LandingPage\LandingPageController;
use App\Http\Controllers\API\Order\PurchaseController;
use App\Http\Controllers\API\Seller\SellerProductController;
use App\Http\Controllers\API\Site\SiteFormController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::prefix('install')->middleware(['install.redirect_if_installed', 'throttle:10,1'])->group(function (): void {
        Route::get('/status', [InstallController::class, 'status'])->name('api.install.status');
        Route::post('/', [InstallController::class, 'store'])->name('api.install.store');
    });

    Route::get('/catalog/products', [CatalogController::class, 'products'])->name('api.catalog.products');

    Route::post('/landing/newsletter', [LandingPageController::class, 'subscribeNewsletter'])
        ->name('api.landing.newsletter');

    Route::post('/site/contact', [SiteFormController::class, 'contact'])->name('api.site.contact');
    Route::post('/site/driver-application', [SiteFormController::class, 'driverApplication'])->name('api.site.driver');
    Route::post('/site/quote-estimate', [SiteFormController::class, 'quoteEstimate'])->name('api.site.quote');
    Route::post('/site/design-me-order', [SiteFormController::class, 'designMeOrder'])->name('api.site.design-me');

    Route::post('/orders/purchase', [PurchaseController::class, 'store'])->name('api.orders.purchase');

    Route::prefix('auth')->middleware('throttle:auth')->group(function (): void {
        Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
        Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
        Route::post('/seller-register', [SellerRegistrationController::class, 'store'])
            ->name('api.auth.seller-register');
    });

    Route::prefix('seller')->middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
        Route::post('/products', [SellerProductController::class, 'store'])->name('api.seller.products.store');
    });

    Route::prefix('auth')->middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
        Route::get('/me', [AuthController::class, 'me'])->name('api.auth.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::post('/logout-other-devices', [AuthController::class, 'logoutOtherDevices'])
            ->name('api.auth.logout-other-devices');
    });
});
