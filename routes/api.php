<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/categories/art', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
