<?php

use App\Http\Controllers\ProductController;
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

