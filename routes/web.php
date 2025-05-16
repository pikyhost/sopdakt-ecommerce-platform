<?php

use App\Livewire\AcceptGuestInvitation;
use App\Livewire\AcceptInvitation;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{Api\NewsletterSubscriberController,
    BostaWebhookController,
    PaymentController,
    ShippingController};

//Route::get('/', function () {
//    return redirect(config('app.frontend_url'));
//});

Route::post('/bosta/webhook', [BostaWebhookController::class, 'handle'])->name('bosta.webhook');

Route::get('/invitation/guest/{invitation}', AcceptGuestInvitation::class)
    ->name('guest.invitation.accept');

Route::middleware('signed')
    ->get('invitation/{invitation}/accept', AcceptInvitation::class)
    ->name('invitation.accept');

Route::get('/newsletter/verify/{id}/{hash}', [NewsletterSubscriberController::class, 'verify'])
    ->name('newsletter.verify')
    ->middleware('signed');

Route::post('/jt-express-webhook', [ShippingController::class, 'handleWebhook']);

Route::get('/payment-success', [PaymentController::class, 'success'])->name('payment.success');

Route::get('/payment-failed', [PaymentController::class, 'failed'])->name('payment.failed');

Route::post('/payment/callback', [App\Http\Controllers\PaymentController::class, 'callback'])->name('payment.callback');
