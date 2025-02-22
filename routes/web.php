<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ProductController, LandingPageController};
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::get('/', function () {return view('welcome');});
Route::get('/category-page', function () {return view('front.category-horizontal-filter2');});
Route::get('/category-page', function () {return view('front.category-horizontal-filter2');});
Route::redirect('/admin/settings', '/admin/settings/1/edit');

Route::group(['prefix' => LaravelLocalization::setLocale(),'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]], function() {
    Route::post('landing-pages/{id}/show-purchase-form', [LandingPageController::class, 'saveBundleData'])->name('landing-pages.purchase-form.save-bundle-data');
    Route::get('landing-pages/{slug}/show-purchase-form', [App\Http\Controllers\LandingPageController::class, 'showPurchaseForm'])->name('landing-pages.purchase-form.show');
    Route::get('landing-page/{slug}', [LandingPageController::class, 'show'])->name('landing-page.show-by-slug');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('product.show');
});

// Route::post('/jt-express-webhook', [ShippingController::class, 'handleWebhook']);
