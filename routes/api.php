<?php

use App\Http\Controllers\Api\Auth\GoogleAuthController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CartListController;
use App\Http\Controllers\Api\CartShippingController;
use App\Http\Controllers\Api\CompareController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\ContactSettingController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\GlobalSearchController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\NewsletterSubscriberController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ServiceFeatureController;
use App\Http\Controllers\Api\SizeRecommendationController;
use App\Http\Controllers\Api\TopNoticeController;
use App\Http\Controllers\Api\WheelController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\BostaWebhookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductRatingController;
use App\Http\Controllers\ShippingController;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/payment/process', [PaymentController::class, 'paymentProcess']);
Route::match(['GET','POST'],'/payment/callback', [PaymentController::class, 'callBack']);


Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/home/featured-categories', [HomeController::class, 'featuredCategories']);
Route::get('/categories/{category:slug}', [CategoryController::class, 'showWithProducts'])->name('categories.show');

Route::get('products/{id}/colors-sizes', [ProductController::class, 'colorsSizes']);

Route::post('/compare', [CompareController::class, 'compare'])->name('compare.add');

Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/fakeBestSellers', [HomeController::class, 'fakeBestSellers']);
Route::get('/products/realBestSellers', [HomeController::class, 'realBestSellers']);
Route::get('/products/best-sellers/manual', [\App\Http\Controllers\Api\HomeController::class, 'manuallySelectedBestSellers']);
Route::get('/products/{slug}', [ProductController::class, 'showBySlug'])->name('products.show');
Route::get('/homepage/slider', [HomeController::class, 'sliderWithCta']);

// Wishlist

Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::get('/wishlist', [WishlistController::class, 'index']);
Route::get('/wishlist/check', [WishlistController::class, 'isWishlisted']);

// Cart (Add to cart and operations)
Route::prefix('cart')->group(function () {
    Route::post('/', [CartController::class, 'store'])->name('cart.add');

    Route::put('/{itemId}', [CartController::class, 'updateQuantity']);
    Route::delete('/{itemId}', [CartController::class, 'destroy']);

    // Cart list and checkout
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
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

Route::post('/newsletter/subscribe', [NewsletterSubscriberController::class, 'store'])->name('newsletter.subscribe');

Route::get('/all-products', [ProductController::class, 'getAllActiveProducts']);

Route::get('/recommended-products', [ProductController::class, 'getRecommendedProducts']);

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
    Route::get('/tags', [BlogController::class, 'getTags']);


    // Authenticated endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/blogs/{blogId}/like', [BlogController::class, 'toggleLike']);
    });
});

Route::get('/global-search', [GlobalSearchController::class, 'search'])->middleware('throttle:60,1');

Route::get('/top-bars', [TopNoticeController::class, 'index']);

Route::get('/footer/contact-info', [HomeController::class, 'footerInfo']);

Route::get('/service-features', [ServiceFeatureController::class, 'index']);

// Public route: guests can see approved product ratings
Route::get('/products/{product}/ratings', [ProductRatingController::class, 'index']);


// Authenticated-only routes: only logged-in users can create/update/delete
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products/{product}/ratings', [ProductRatingController::class, 'store']);
    Route::put('/products/{product}/ratings/{rating}', [ProductRatingController::class, 'update']);
    Route::delete('/products/{product}/ratings/{rating}', [ProductRatingController::class, 'destroy']);
});

Route::get('/get-user-id', function () {
   if (auth()->check()) {
       return auth()->id();
   }
   return 'user not login to get his id';
});

Route::get('/cart/nested-related-data', [CartController::class, 'getNestedCartData']);

Route::get('/cart/related-data', [CartListController::class, 'getRelatedCartData']);
Route::get('/discounts', [DiscountController::class, 'index']);
Route::prefix('coupons')->group(function () {
    Route::post('/apply', [CouponController::class, 'apply']);
    Route::post('/remove', [CouponController::class, 'remove']);

// New product coupon routes
    Route::post('/apply-to-product', [CouponController::class, 'applyToProduct']);
    Route::post('/apply-to-cart-item', [CouponController::class, 'applyProductCouponToCart']);
});


Route::get('/faqs', [FaqController::class, 'index']);

Route::get('/pixels', function () {
    $settings = Setting::first();

    return response()->json([
        'google_pixel' => $settings?->google_pixel,
        'meta_pixel' => $settings?->meta_pixel,
    ]);
});

Route::get('/cart/check-free-shipping', [CartShippingController::class, 'check']);

Route::get('/free-shipping-amount', function () {
    return  Setting::first()?->free_shipping_threshold;
});



Route::get('/get-phone-number', function () {
    return response()->json([
        'response' => 'success',
        'phone' => Setting::first()->phone,
    ]);
});

Route::post('/cart/apply-coupon', [CartListController::class, 'applyCoupon'])->name('cart.apply-coupon');
Route::post('/cart/remove-coupon', [CartListController::class, 'removeCoupon'])->name('cart.remove-coupon');

Route::get('/size-guide-image', function () {
    $setting = Setting::first(); // or Setting::singleton() if you're using a singleton pattern

    if (!$setting || !$setting->getSizeGuideProductImageUrl()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Size guide image not found.',
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'image_url' => $setting->getSizeGuideProductImageUrl(),
    ]);
});


Route::post('/recommend-size', [SizeRecommendationController::class, 'recommend']);

Route::prefix('wheel')->group(function () {
    // Get active wheel for current page
    Route::get('/active', [WheelController::class, 'getActiveWheel']);

    // Check if user can spin
    Route::get('/eligibility', [WheelController::class, 'checkEligibility']);

    // Perform a spin
    Route::post('/spin', [WheelController::class, 'spin']);

//    // Get user's spin history
//    Route::get('/history', [WheelController::class, 'spinHistory'])->middleware('auth:sanctum');

    // Set "don't show again" preference
//    Route::post('/hide', [WheelController::class, 'hideWheel']);
});


Route::get('/google-analytics', function () {
    try {
        $settings = DB::table('settings')->first();

        if (!$settings || empty($settings->google_analytics)) {
            return response()->json([
                'success' => false,
                'message' => 'Google Analytics code not found',
                'code' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'code' => $settings->google_analytics
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve Google Analytics code',
            'error' => $e->getMessage()
        ], 500);
    }
});


Route::post('/auth/google', [GoogleAuthController::class, 'login']);
