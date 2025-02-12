<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('test', function (){
    $ip = app()->isLocal() ? '156.221.68.3' : request()->ip();  // Egypt ip

    $location = geoip($ip);

    // Log the entire location data
    Log::info('GeoIP Location Data:', $location->toArray());

    return $location['country_code2'] ?? 'US';
});

Route::get('/category-page', function () {
    return view('front.category-horizontal-filter2');
});

Route::get('/category-page', function () {
    return view('front.category-horizontal-filter2');
});

Route::get('/products/{slug}', [ProductController::class, 'show'])->name('product.show');


Route::post('/wishlist/toggle', [ProductController::class, 'toggleWishlist'])->middleware('auth');
Route::get('/wishlist/check/{productId}', [ProductController::class, 'checkWishlist'])->middleware('auth');


Route::get('test-push', function () {
    return 'success';
});
