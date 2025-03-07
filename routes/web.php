<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\{CartController,
    CheckoutController,
    OrderCompleteController,
    ProductController,
    LandingPageController,
    RegionsController,
    ShippingController,
    CategoryProductController};

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


Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {
    Route::get('landing-page/{slug}', [LandingPageController::class, 'show'])->name('landing-page.show-by-slug');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/category/{slug}', [CategoryProductController::class, 'show'])->name('category.products');
    Route::get('/regions', [RegionsController::class, 'index'])->name('regions.index');
    Route::post('/calculate-shipping', [ShippingController::class, 'calculateShipping'])->name('shipping.calculate');
    Route::post('landing-pages/{id}/show-purchase-form', [LandingPageController::class, 'saveBundleData'])->name('landing-pages.purchase-form.save-bundle-data');
    Route::get('landing-pages/{slug}/show-purchase-form', [LandingPageController::class, 'showPurchaseForm'])->name('landing-pages.purchase-form.show');
    Route::post('landing-pages/{id}/purchase', [LandingPageController::class, 'order'])->name('landing-pages.purchase-form.store');
    Route::post('/landing-pages/{id}/get-combination-price', [LandingPageController::class, 'getCombinationPrice'])->name('dashboard.landing-pages.get-combination-price');
    Route::post('landing-pages/{id}/order', [LandingPageController::class, 'saveOrder'])->name('landing-pages.purchase-form.order');
    Route::get('landing-pages/{slug}/thanks', [LandingPageController::class, 'thanks'])->name('landing-pages.thanks');

    Route::get('/my-cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/order-complete', [OrderCompleteController::class, 'index'])->name('order.complete');
});

Route::post('/jt-express-webhook', [ShippingController::class, 'handleWebhook']);
