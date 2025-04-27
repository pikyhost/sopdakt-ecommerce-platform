<?php

use App\Livewire\AcceptGuestInvitation;
use App\Livewire\AcceptInvitation;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\{AramexWebhookController,
    BostaWebhookController,
    LandingPageController,
    RegionsController,
    ShippingController,
    CategoryProductController,
    };

// This route catches everything except `admin/*` and `client/*`
Route::get('{any}', function () {
    abort(404);
})->where('any', '^(?!admin|client).*$');

Route::middleware('signed')
    ->get('invitation/{invitation}/accept', AcceptInvitation::class)
    ->name('invitation.accept');

Route::get('/invitation/guest/{invitation}', AcceptGuestInvitation::class)
    ->name('guest.invitation.accept');

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {

    Route::get('landing-page/{slug}', [LandingPageController::class, 'show'])->name('landing-page.show-by-slug');
    Route::get('/category/{slug}', [CategoryProductController::class, 'show'])->name('category.products');
    Route::get('/regions', [RegionsController::class, 'index'])->name('regions.index');
    Route::post('/calculate-shipping', [ShippingController::class, 'calculateShipping'])->name('shipping.calculate');
    Route::post('landing-pages/{id}/show-purchase-form', [LandingPageController::class, 'saveBundleData'])->name('landing-page.purchase-form.save-bundle-data');
    Route::get('landing-pages/{slug}/show-purchase-form', [LandingPageController::class, 'showPurchaseForm'])->name('landing-page.purchase-form.show');
    Route::post('landing-pages/{id}/purchase', [LandingPageController::class, 'order'])->name('landing-page.purchase-form.store');
    Route::post('/landing-pages/{id}/get-combination-price', [LandingPageController::class, 'getCombinationPrice'])->name('landing-page.get-combination-price');
    Route::post('landing-pages/{id}/order', [LandingPageController::class, 'saveOrder'])->name('landing-page.purchase-form.order');
    Route::get('landing-pages/{slug}/thanks', [LandingPageController::class, 'thanks'])->name('landing-pages.thanks');

});
Route::post('/jt-express-webhook', [ShippingController::class, 'handleWebhook']);

Route::post('/payment/callback', [App\Http\Controllers\PaymentController::class, 'callback'])->name('payment.callback');

Route::post('/aramex/webhook', [AramexWebhookController::class, 'handle'])->name('aramex.webhook');

Route::post('/webhooks/bosta', [BostaWebhookController::class, 'handle'])->name('bosta.webhook');
