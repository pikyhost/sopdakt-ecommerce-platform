<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/categories/art', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/regions', [\App\Http\Controllers\Api\RegionsController::class, 'index'])->name('api.regions.index');
Route::post('/calculate-shipping', [\App\Http\Controllers\Api\ShippingController::class, 'calculateShipping'])->name('api.shipping.calculate');
Route::post('/landing-pages/{id}/get-combination-price', [\App\Http\Controllers\LandingPageController::class, 'getCombinationPrice'])->name('dashboard.landing-pages.get-combination-price');

