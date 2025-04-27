<?php

use App\Http\Controllers\Api\CompareController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShippingController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('jt-express-webhook', [ShippingController::class, 'handleWebhook']);

Route::post('/payment/process', [PaymentController::class, 'paymentProcess']);
Route::match(['GET','POST'],'/payment/callback', [PaymentController::class, 'callBack']);

Route::get('/home/featured-categories', [HomeController::class, 'featuredCategories']);

Route::prefix('cart')->controller(\App\Http\Controllers\Api\CartController::class)->group(function () {
    Route::get('/', 'index'); // Load cart
    Route::post('/', 'store'); // Add to cart
    Route::put('/{itemId}', 'updateQuantity'); // Update quantity
    Route::delete('/{itemId}', 'destroy'); // Remove from cart
});

Route::get('products/{product}/colors-sizes', [\App\Http\Controllers\Api\ProductController::class, 'colorsSizes']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::get('/wishlist/{productId}', [WishlistController::class, 'isWishlisted']);
});

Route::post('/compare', [CompareController::class, 'compare']);

Route::get('/products/{slug}', [ProductController::class, 'showBySlug'])->name('products.show');

Route::get('/products/featured', [ProductController::class, 'featured']);

Route::get('/homepage/slider', [HomeController::class, 'sliderWithCta']);


Route::prefix('cart')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\CartListController::class, 'index'])->name('api.cart.index');
    Route::post('/shipping', [\App\Http\Controllers\Api\CartListController::class, 'updateShipping'])->name('api.cart.updateShipping');
    Route::post('/item/{cartItemId}/quantity', [\App\Http\Controllers\Api\CartListController::class, 'updateQuantity'])->name('api.cart.updateQuantity');
    Route::delete('/item/{cartItemId}', [\App\Http\Controllers\Api\CartListController::class, 'removeItem'])->name('api.cart.removeItem');
    Route::post('/checkout', [\App\Http\Controllers\Api\CartListController::class, 'checkout'])->name('api.cart.checkout');
});
