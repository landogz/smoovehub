<?php

use App\Http\Controllers\Web\Install\InstallPageController;
use App\Http\Controllers\Web\Site\SitePageController;
use Illuminate\Support\Facades\Route;

Route::get('/install', [InstallPageController::class, 'index'])
    ->middleware('install.redirect_if_installed')
    ->name('install.index');

Route::controller(SitePageController::class)->group(function (): void {
    Route::get('/', 'home')->name('site.home');
    Route::get('/about', 'about')->name('site.about');
    Route::get('/shop', 'shop')->name('site.shop');
    Route::get('/sell', 'sell')->name('site.sell');
    Route::get('/sellers', 'sellers')->name('site.sellers');
    Route::get('/store/{slug}', 'sellerStore')->name('site.store.show');
    Route::get('/cities', 'cities')->name('site.cities');
    Route::get('/cities/{city}', 'cityShow')->name('site.cities.show');
    Route::get('/blog', 'blog')->name('site.blog');
    Route::get('/privacy', 'privacy')->name('site.privacy');
    Route::get('/terms', 'terms')->name('site.terms');
    Route::get('/contact', 'contact')->name('site.contact');
    Route::get('/quote', 'quote')->name('site.quote');
    Route::get('/drive', 'drive')->name('site.drive');
    Route::get('/design-room', 'designRoom')->name('site.design-room');
    Route::get('/design-me', 'designMe')->name('site.design-me');
    Route::get('/dashboard', 'dashboard')->name('site.dashboard');
    Route::get('/distribute', 'distribute')->name('site.distribute');
    Route::get('/admin', 'admin')->name('site.admin');
});

Route::redirect('/landing', '/')->name('landing.index');
