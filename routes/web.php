<?php

use App\Livewire\AcceptGuestInvitation;
use App\Livewire\AcceptInvitation;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\{CartController,
    CheckoutController,
    HomePageController,
    OrderCompleteController,
    ProductController,
    LandingPageController,
    RegionsController,
    ShippingController,
    CategoryProductController,
    WishlistController};

Route::redirect('/admin/settings', '/admin/settings/1/edit');
Route::redirect('/admin/home-page-settings', '/admin/home-page-settings/1/edit');


Route::middleware('signed')
    ->get('invitation/{invitation}/accept', AcceptInvitation::class)
    ->name('invitation.accept');


Route::get('/invitation/guest/{invitation}', AcceptGuestInvitation::class)
    ->name('guest.invitation.accept');

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {

    Route::get('/', [HomePageController::class, 'index'])->name('homepage');

    Route::get('/about-us', function () {
        return 'To be the about us page';
    });

    Route::get('/contact-us', function () {
        return 'To be the contact us page';
    });

    Route::view('demo', 'front.demo');

    Route::get('/blogs', function () {
        return 'To be the blogs page';
    });

    Route::get('/products', function () {
        return 'To be the products page';
    })->name('products');

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

    Route::get('wishlist', [WishlistController::class, 'index']);
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/order-success', [OrderCompleteController::class, 'index'])->name('order.complete');
});
Route::post('/jt-express-webhook', [ShippingController::class, 'handleWebhook']);

Route::get('/test-email', function () {
    $order = Order::query()->orderByDesc('id')->first(); // Replace with a valid ID
    return new \App\Mail\OrderStatusMail($order, \App\Enums\OrderStatus::Pending);
});