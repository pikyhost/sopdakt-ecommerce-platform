<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShippingController;

Route::get('/categories/art', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('jt-express-webhook', [ShippingController::class, 'handleWebhook']);

