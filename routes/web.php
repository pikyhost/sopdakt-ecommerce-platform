<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ProductController, LandingPageController, RegionsController, ShippingController, CategoryProductController};
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/category-page', function () {
    return view('front.category-horizontal-filter2');
});

Route::get('/category-page', function () {
    return view('front.category-horizontal-filter2');
});

Route::redirect('/admin/settings', '/admin/settings/1/edit');
Route::get('/category/{slug}', [CategoryProductController::class, 'show'])->name('category.products');

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {
    Route::post('landing-pages/{id}/show-purchase-form', [LandingPageController::class, 'saveBundleData'])->name('landing-pages.purchase-form.save-bundle-data');
    Route::get('landing-pages/{slug}/show-purchase-form', [LandingPageController::class, 'showPurchaseForm'])->name('landing-pages.purchase-form.show');
    Route::get('landing-page/{slug}', [LandingPageController::class, 'show'])->name('landing-page.show-by-slug');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/regions', [RegionsController::class, 'index'])->name('regions.index');
    Route::post('/calculate-shipping', [ShippingController::class, 'calculateShipping'])->name('shipping.calculate');
    Route::post('/landing-pages/{id}/get-combination-price', [LandingPageController::class, 'getCombinationPrice'])->name('dashboard.landing-pages.get-combination-price');
    Route::post('landing-pages/{id}/order', [LandingPageController::class, 'saveOrder'])->name('landing-pages.purchase-form.order');
});

// Route::post('/jt-express-webhook', [ShippingController::class, 'handleWebhook']);

Route::get('/test-git', function () {
    return 'working';
});
