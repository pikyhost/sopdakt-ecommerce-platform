<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CartListController;
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

require __DIR__.'/auth.php';

Route::post('jt-express-webhook', [ShippingController::class, 'handleWebhook']);

Route::post('/payment/process', [PaymentController::class, 'paymentProcess']);
Route::match(['GET','POST'],'/payment/callback', [PaymentController::class, 'callBack']);

Route::get('/home/featured-categories', [HomeController::class, 'featuredCategories']);

Route::get('products/{product}/colors-sizes', [\App\Http\Controllers\Api\ProductController::class, 'colorsSizes']);

Route::post('/compare', [CompareController::class, 'compare'])->name('compare.add');

Route::get('/products/featured', [ProductController::class, 'featured']); //bestSellers
Route::get('/products/bestSellers', [HomeController::class, 'bestSellers']); //bestSellers
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
    Route::get('/', [CartListController::class, 'index'])->name('api.cart.index');
    Route::post('/shipping', [CartListController::class, 'updateShipping'])->name('api.cart.updateShipping');
    Route::post('/item/{cartItemId}/quantity', [CartListController::class, 'updateQuantity'])->name('api.cart.updateQuantity');
    Route::delete('/item/{cartItemId}', [CartListController::class, 'removeItem'])->name('api.cart.removeItem');
    Route::post('/checkout', [CartListController::class, 'checkout'])->name('api.cart.checkout');
});
