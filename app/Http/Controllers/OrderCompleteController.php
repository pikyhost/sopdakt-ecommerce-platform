<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class OrderCompleteController extends Controller
{
    public function index(Request $request)
    {
        $currentRoute = Route::currentRouteName(); // Set route in mount
        // Check if the user is redirected from order placement
        if (!session()->has('order_success')) {
            return redirect()->route('cart.index')->with('error', 'You have no recent order.');
        }

        return view('front.order-complete', compact('currentRoute'));
    }
}
