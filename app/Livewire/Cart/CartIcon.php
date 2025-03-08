<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartIcon extends Component
{
    public $cartCount = 0;

    protected $listeners = ['cartUpdated' => 'updateCartCount'];

    public function mount()
    {
        $this->updateCartCount();
    }

    public function updateCartCount()
    {
        $this->cartCount = $this->getCartCount();
    }

    private function getCartCount()
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->withCount('items')->first()?->items_count ?? 0;
        } else {
            $session_id = Session::getId();
            return Cart::where('session_id', $session_id)->withCount('items')->first()?->items_count ?? 0;
        }
    }

    public function goToCart()
    {
        return redirect()->route('cart.index');
    }

    public function render()
    {
        return view('livewire.cart.cart-icon');
    }
}
