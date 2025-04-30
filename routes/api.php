<?php

use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CartListController;
use App\Http\Controllers\Api\CompareController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\ContactSettingController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShippingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

require __DIR__.'/auth.php';

Route::post('jt-express-webhook', [ShippingController::class, 'handleWebhook']);

Route::post('/payment/process', [PaymentController::class, 'paymentProcess']);
Route::match(['GET','POST'],'/payment/callback', [PaymentController::class, 'callBack']);

Route::get('/home/featured-categories', [HomeController::class, 'featuredCategories']);

Route::get('products/{id}/colors-sizes', [ProductController::class, 'colorsSizes']);

Route::post('/compare', [CompareController::class, 'compare'])->name('compare.add');

Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/fakeBestSellers', [HomeController::class, 'fakeBestSellers']);
Route::get('/products/realBestSellers', [HomeController::class, 'realBestSellers']);
Route::get('/products/{slug}', [ProductController::class, 'showBySlug'])->name('products.show');
Route::get('/homepage/slider', [HomeController::class, 'sliderWithCta']);

// Wishlist
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::get('/wishlist/{productId}', [WishlistController::class, 'isWishlisted']);
});

// Cart (Add to cart and operations)
Route::prefix('cart')->group(function () {
    Route::post('/', [CartController::class, 'store'])->name('cart.add');
    Route::put('/{itemId}', [CartController::class, 'updateQuantity']);
    Route::delete('/{itemId}', [CartController::class, 'destroy']);

    // Cart list and checkout
    Route::get('/', [CartListController::class, 'index'])->name('cart.index');
    Route::post('/shipping', [CartListController::class, 'updateShipping'])->name('cart.updateShipping');
    Route::post('/item/{cartItemId}/quantity', [CartListController::class, 'updateQuantity'])->name('cart.updateQuantity');
    Route::delete('/item/{cartItemId}', [CartListController::class, 'removeItem'])->name('cart.removeItem');
    Route::post('/checkout', [CartListController::class, 'checkout'])->name('checkout');
});

Route::get('/about-us', [AboutUsController::class, 'index'])->name('about-us.index');
Route::get('/contact-settings', [ContactSettingController::class, 'index'])->name('contact-settings.index');
Route::post('/contact', [ContactMessageController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']); // List user orders
    Route::get('/orders/track/{tracking_number}', [OrderController::class, 'track']); // Track order by tracking number
    Route::put('/orders/{order}', [OrderController::class, 'update']); // Edit own order
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']); // Delete own order
});
