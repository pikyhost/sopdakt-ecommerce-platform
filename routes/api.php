<?php

use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CartListController;
use App\Http\Controllers\Api\CompareController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\ContactSettingController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\GlobalSearchController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\NewsletterSubscriberController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TopNoticeController;
use App\Http\Controllers\Api\WheelController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\BostaWebhookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShippingController;

require __DIR__.'/auth.php';

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('jt-express-webhook', [ShippingController::class, 'handleWebhook']);

Route::post('/bosta/webhook', [BostaWebhookController::class, 'handle'])->name('bosta.webhook');

Route::prefix('aramex')->group(function () {
    Route::post('webhook', [\App\Http\Controllers\Api\AramexController::class, 'webhook']);
    Route::post('orders/{order}/create-shipment', [\App\Http\Controllers\Api\AramexController::class, 'createShipment'])
        ->name('api.aramex.orders.create-shipment');
    Route::get('orders/{order}/track-shipment', [\App\Http\Controllers\Api\AramexController::class, 'trackShipment']);
});

/*
 *

Failed to create shipment
Route [api.aramex.orders.create-shipment] not defined.

 *
 * */

Route::post('/payment/process', [PaymentController::class, 'paymentProcess']);
Route::match(['GET','POST'],'/payment/callback', [PaymentController::class, 'callBack']);

Route::get('/home/featured-categories', [HomeController::class, 'featuredCategories']);
Route::get('/categories/{category:slug}', [CategoryController::class, 'showWithProducts']);


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

Route::prefix('policies')->group(function () {
    Route::get('/privacy', [App\Http\Controllers\Api\PolicyController::class, 'privacy'])->name('policies.privacy');
    Route::get('/refund', [App\Http\Controllers\Api\PolicyController::class, 'refund'])->name('policies.refund');
    Route::get('/terms', [App\Http\Controllers\Api\PolicyController::class, 'terms'])->name('policies.terms');
    Route::get('/shipping', [App\Http\Controllers\Api\PolicyController::class, 'shipping'])->name('policies.shipping');
});

Route::prefix('banners')->group(function () {
    Route::get('/product', [App\Http\Controllers\Api\BannerController::class, 'product'])->name('banners.product');
    Route::get('/category', [App\Http\Controllers\Api\BannerController::class, 'category'])->name('banners.category');
});

Route::get('/popups', [App\Http\Controllers\Api\PopupController::class, 'index'])->name('popups.index');
Route::post('/checkout', [App\Http\Controllers\Api\CheckoutController::class, 'store'])->name('checkout.store');

// Wheel of Fortune Routes
Route::prefix('wheel')->group(function () {
    Route::get('/', [WheelController::class, 'index'])->name('wheel.index');
    Route::post('/spin', [WheelController::class, 'spin'])->name('wheel.spin');
});

Route::get('/discounts', [DiscountController::class, 'index']);

Route::post('/newsletter/subscribe', [NewsletterSubscriberController::class, 'store'])->name('newsletter.subscribe');

Route::get('/all-products', [ProductController::class, 'getAllActiveProducts']);

Route::get('/recommended-products', [ProductController::class, 'getRecommendedProducts']);

Route::group(['prefix' => 'blogs', 'as' => 'api.blogs.'], function () {
    Route::get('categories', [BlogController::class, 'getCategories'])->name('categories');
    Route::get('/', [BlogController::class, 'getBlogs'])->name('index');
    Route::get('{slug}', [BlogController::class, 'getBlogBySlug'])->name('show');
    Route::get('tags', [BlogController::class, 'getTags'])->name('tags');
    Route::post('{blogId}/toggle-like', [BlogController::class, 'toggleLike'])
        ->name('toggle-like')->middleware('auth:sanctum');
});

Route::middleware('api')->group(function () {
    // Public blog endpoints
    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/blogs/categories', [BlogController::class, 'categories']);
    Route::get('/blogs/popular', [BlogController::class, 'popular']);
    Route::get('/blogs/recent', [BlogController::class, 'recent']);
    Route::get('/blogs/search', [BlogController::class, 'search']);
    Route::get('/blogs/category/{categorySlug}', [BlogController::class, 'byCategory']);
    Route::get('/blogs/tag/{tagId}', [BlogController::class, 'byTag']);
    Route::get('/blogs/{slug}', [BlogController::class, 'show']);

    // Authenticated endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/blogs/{blogId}/like', [BlogController::class, 'toggleLike']);
    });
});

Route::get('/global-search', [GlobalSearchController::class, 'search'])->middleware('throttle:60,1');
Route::get('/top-bars', [TopNoticeController::class, 'index']);
