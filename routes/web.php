<?php

use App\Livewire\AcceptGuestInvitation;
use App\Livewire\AcceptInvitation;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\{CartController,
    CheckoutController,
    HomePageController,
    OrderCompleteController,
    ProductComparisonController,
    ProductController,
    LandingPageController,
    RegionsController,
    ShippingController,
    CategoryProductController,
    WishlistController};

use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

Route::middleware('signed')
    ->get('invitation/{invitation}/accept', AcceptInvitation::class)
    ->name('invitation.accept');


Route::get('/invitation/guest/{invitation}', AcceptGuestInvitation::class)
    ->name('guest.invitation.accept');

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {

    Route::get('/', [HomePageController::class, 'index'])->name('homepage');

    Route::view('/blogs', 'pages.blogs')->name('blogs');
    Route::view('/products', 'pages.products')->name('products');
    Route::view('/categories', 'pages.categories')->name('categories');

    Route::get('test-about-use', view('front.front_about'));

    Route::get('landing-page/{slug}', [LandingPageController::class, 'show'])->name('landing-page.show-by-slug');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/category/{slug}', [CategoryProductController::class, 'show'])->name('category.products');
    Route::get('/regions', [RegionsController::class, 'index'])->name('regions.index');
    Route::post('/calculate-shipping', [ShippingController::class, 'calculateShipping'])->name('shipping.calculate');
    Route::post('landing-pages/{id}/show-purchase-form', [LandingPageController::class, 'saveBundleData'])->name('landing-page.purchase-form.save-bundle-data');
    Route::get('landing-pages/{slug}/show-purchase-form', [LandingPageController::class, 'showPurchaseForm'])->name('landing-page.purchase-form.show');
    Route::post('landing-pages/{id}/purchase', [LandingPageController::class, 'order'])->name('landing-page.purchase-form.store');
    Route::post('/landing-pages/{id}/get-combination-price', [LandingPageController::class, 'getCombinationPrice'])->name('landing-page.get-combination-price');
    Route::post('landing-pages/{id}/order', [LandingPageController::class, 'saveOrder'])->name('landing-page.purchase-form.order');
    Route::get('landing-pages/{slug}/thanks', [LandingPageController::class, 'thanks'])->name('landing-pages.thanks');

    Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/order-success', [OrderCompleteController::class, 'index'])->name('order.complete');

    Route::view('/privacy-policy', 'pages.privacy-policy')->name('privacy.policy');
    Route::view('/about-us', 'pages.about-us')->name('about.us');
    Route::view('/contact-us', 'pages.contact-us')->name('contact.us');
    Route::view('/refund-policy', 'pages.refund-policy')->name('refund.policy');
    Route::view('/terms-of-service', 'pages.terms-of-service')->name('terms.of.service');
    Route::get('/compare-products/{ids}', [ProductComparisonController::class, 'index'])->name('compare.products');

    Route::get('/search/{query}', function ($query) {
        return view('search-results', ['query' => $query]);
    })->name('search.results');
});
Route::post('/jt-express-webhook', [ShippingController::class, 'handleWebhook']);

Route::get('/analytics', function () {
    $analyticsData = Analytics::fetchTotalVisitorsAndPageViews(Period::days(7));

    return response()->json($analyticsData);
});

Route::get('/test-analytics', function () {
    try {
        $analyticsData = Analytics::fetchVisitorsAndPageViews(Period::days(7));

        return response()->json($analyticsData);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
