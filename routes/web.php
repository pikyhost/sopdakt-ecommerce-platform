<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/category-page', function () {
    return view('front.category-horizontal-filter2');
});

Route::get('/category-page', function () {
    return view('front.category-horizontal-filter2');
});

Route::redirect('/admin/settings', '/admin/settings/1/edit');

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
    ], function() {
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('product.show');
});

Route::get('/test', fn() => 'all working');

Route::get('test', function (){
    $ip = app()->isLocal() ? '156.221.68.3' : request()->ip();  // Egypt ip

    $location = geoip($ip);

    // Log the entire location data
    Log::info('GeoIP Location Data:', $location->toArray());

    return $location['country_code2'] ?? 'US';
});
