<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\SellerRegistrationController;
use App\Http\Controllers\API\Catalog\CatalogController;
use App\Http\Controllers\API\Install\InstallController;
use App\Http\Controllers\API\LandingPage\LandingPageController;
use App\Http\Controllers\API\Order\PurchaseController;
use App\Http\Controllers\API\Member\MemberAccountController;
use App\Http\Controllers\API\Member\MemberAddressController;
use App\Http\Controllers\API\Member\MemberNotificationController;
use App\Http\Controllers\API\Member\MemberOrderController;
use App\Http\Controllers\API\Member\MemberPaymentMethodController;
use App\Http\Controllers\API\Member\MemberVoucherController;
use App\Http\Controllers\API\Seller\SellerDashboardController;
use App\Http\Controllers\API\Seller\SellerProductController;
use App\Http\Controllers\API\Site\AddressSuggestController;
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

    Route::get('/site/address-suggest', AddressSuggestController::class)
        ->middleware('throttle:address-suggest')
        ->name('api.site.address-suggest');

    Route::post('/site/contact', [SiteFormController::class, 'contact'])->name('api.site.contact');
    Route::post('/site/driver-application', [SiteFormController::class, 'driverApplication'])->name('api.site.driver');
    Route::post('/site/quote-estimate', [SiteFormController::class, 'quoteEstimate'])->name('api.site.quote');
    Route::post('/site/design-me-order', [SiteFormController::class, 'designMeOrder'])->name('api.site.design-me');

    Route::middleware('throttle:api')->group(function (): void {
        Route::post('/orders/quote', [PurchaseController::class, 'quote'])->name('api.orders.quote');
        Route::post('/orders/purchase', [PurchaseController::class, 'store'])
            ->middleware(['auth:sanctum', 'role:customer'])
            ->name('api.orders.purchase');
    });

    Route::prefix('auth')->middleware('throttle:auth')->group(function (): void {
        Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
        Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
        Route::post('/seller-register', [SellerRegistrationController::class, 'store'])
            ->name('api.auth.seller-register');
    });

    Route::prefix('member')->middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
        Route::get('/account', [MemberAccountController::class, 'show'])->name('api.member.account');
        Route::put('/profile', [MemberAccountController::class, 'updateProfile'])->name('api.member.profile');
        Route::post('/avatar', [MemberAccountController::class, 'storeAvatar'])->name('api.member.avatar');
        Route::put('/password', [MemberAccountController::class, 'updatePassword'])->name('api.member.password');
        Route::put('/settings', [MemberAccountController::class, 'updateSettings'])->name('api.member.settings');

        Route::get('/addresses', [MemberAddressController::class, 'index'])->name('api.member.addresses.index');
        Route::post('/addresses', [MemberAddressController::class, 'store'])->name('api.member.addresses.store');
        Route::put('/addresses/{uuid}', [MemberAddressController::class, 'update'])->name('api.member.addresses.update');
        Route::delete('/addresses/{uuid}', [MemberAddressController::class, 'destroy'])->name('api.member.addresses.destroy');
        Route::post('/addresses/{uuid}/default', [MemberAddressController::class, 'setDefault'])->name('api.member.addresses.default');

        Route::get('/payment-methods', [MemberPaymentMethodController::class, 'index'])->name('api.member.payments.index');
        Route::post('/payment-methods', [MemberPaymentMethodController::class, 'store'])->name('api.member.payments.store');
        Route::delete('/payment-methods/{uuid}', [MemberPaymentMethodController::class, 'destroy'])->name('api.member.payments.destroy');
        Route::post('/payment-methods/{uuid}/default', [MemberPaymentMethodController::class, 'setDefault'])->name('api.member.payments.default');

        Route::get('/orders', [MemberOrderController::class, 'index'])->name('api.member.orders');

        Route::get('/notifications', [MemberNotificationController::class, 'index'])->name('api.member.notifications');
        Route::post('/notifications/read-all', [MemberNotificationController::class, 'markAllRead'])->name('api.member.notifications.read-all');
        Route::post('/notifications/{id}/read', [MemberNotificationController::class, 'markRead'])->name('api.member.notifications.read');

        Route::get('/vouchers', [MemberVoucherController::class, 'index'])->name('api.member.vouchers.index');
        Route::post('/vouchers/redeem', [MemberVoucherController::class, 'redeem'])->name('api.member.vouchers.redeem');
    });

    Route::prefix('seller')->middleware(['auth:sanctum', 'role:seller', 'throttle:api'])->group(function (): void {
        Route::get('/dashboard', [SellerDashboardController::class, 'overview'])->name('api.seller.dashboard');
        Route::get('/orders', [SellerDashboardController::class, 'orders'])->name('api.seller.orders');
        Route::patch('/orders/{uuid}', [SellerDashboardController::class, 'updateOrder'])->name('api.seller.orders.update');
        Route::get('/payouts', [SellerDashboardController::class, 'payouts'])->name('api.seller.payouts');
        Route::post('/payout-accounts', [SellerDashboardController::class, 'storePayoutAccount'])->name('api.seller.payout-accounts.store');
        Route::delete('/payout-accounts/{uuid}', [SellerDashboardController::class, 'destroyPayoutAccount'])->name('api.seller.payout-accounts.destroy');
        Route::post('/payout-accounts/{uuid}/default', [SellerDashboardController::class, 'defaultPayoutAccount'])->name('api.seller.payout-accounts.default');
        Route::get('/vouchers', [SellerDashboardController::class, 'vouchers'])->name('api.seller.vouchers.index');
        Route::post('/vouchers', [SellerDashboardController::class, 'storeVoucher'])->name('api.seller.vouchers.store');
        Route::put('/vouchers/{uuid}', [SellerDashboardController::class, 'updateVoucher'])->name('api.seller.vouchers.update');
        Route::delete('/vouchers/{uuid}', [SellerDashboardController::class, 'destroyVoucher'])->name('api.seller.vouchers.destroy');
        Route::get('/settings', [SellerDashboardController::class, 'settings'])->name('api.seller.settings.show');
        Route::put('/settings', [SellerDashboardController::class, 'updateSettings'])->name('api.seller.settings.update');
        Route::post('/logo', [SellerDashboardController::class, 'storeLogo'])->name('api.seller.logo');
        Route::get('/dist-catalog', [SellerDashboardController::class, 'distCatalog'])->name('api.seller.dist-catalog');
        Route::post('/dist-catalog/{uuid}/import', [SellerDashboardController::class, 'importDistSku'])->name('api.seller.dist-catalog.import');
        Route::get('/imports', [SellerDashboardController::class, 'imports'])->name('api.seller.imports');
        Route::get('/purchases', [SellerDashboardController::class, 'purchases'])->name('api.seller.purchases');
        Route::get('/onsite-appointments', [SellerDashboardController::class, 'onsiteAppointments'])->name('api.seller.onsite');
        Route::patch('/onsite-appointments/{uuid}', [SellerDashboardController::class, 'updateOnsite'])->name('api.seller.onsite.update');
        Route::get('/open-api', [SellerDashboardController::class, 'openApi'])->name('api.seller.open-api');
        Route::post('/open-api/token', [SellerDashboardController::class, 'issueOpenApiToken'])->name('api.seller.open-api.token');
        Route::delete('/open-api/token', [SellerDashboardController::class, 'revokeOpenApiToken'])->name('api.seller.open-api.revoke');
        Route::get('/products', [SellerProductController::class, 'index'])->name('api.seller.products.index');
        Route::post('/products', [SellerProductController::class, 'store'])->name('api.seller.products.store');
        Route::put('/products/{uuid}', [SellerProductController::class, 'update'])->name('api.seller.products.update');
        Route::delete('/products/{uuid}', [SellerProductController::class, 'destroy'])->name('api.seller.products.destroy');
    });

    Route::prefix('auth')->middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
        Route::get('/me', [AuthController::class, 'me'])->name('api.auth.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::post('/logout-other-devices', [AuthController::class, 'logoutOtherDevices'])
            ->name('api.auth.logout-other-devices');
    });
});
