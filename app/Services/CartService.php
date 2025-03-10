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
            $cartQuery = Cart::with([
                'items' => function ($query) {
                    $query->with([
                        'product:id,name,price,slug',
                        'product.category:id,name',
                        'product.media',  // Load media in one query
                        'product.inventory', // Load inventory in one query
                        'size:id,name',
                        'color:id,name,code'
                    ]);
                }
            ]);

            self::$cart = Auth::check()
                ? $cartQuery->where('user_id', Auth::id())->latest()->first()
                : $cartQuery->where('session_id', Session::get('cart_session', Session::getId()))->latest()->first();
        }

        return self::$cart;
    }

    public static function getGuestContact()
    {
        return Contact::where('session_id', Session::get('cart_session', Session::getId()))->first();
    }
}
