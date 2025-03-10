<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected static $cart = null;

    public static function getCart()
    {
        if (self::$cart === null) {
            self::$cart = Auth::check()
                ? Cart::where('user_id', Auth::id())->with('items')->latest()->first()
                : Cart::where('session_id', Session::get('cart_session', Session::getId()))->with('items')->latest()->first();
        }
        return self::$cart;
    }

    public static function getGuestContact()
    {
        return Contact::where('session_id', Session::get('cart_session', Session::getId()))->first();
    }
}
