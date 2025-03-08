<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartIcon extends Component
{
    public $cartCount = 0;
    public $cartItems = [];
    public $subtotal = 0;

    protected $listeners = ['cartUpdated' => 'updateCart'];

    public function mount()
    {
        $this->updateCart();
    }

    public function updateCart()
    {
        $cart = $this->getCart();

        $this->cartCount = $cart?->items()->sum('quantity') ?? 0;
        $this->cartItems = $cart?->items()->with(['product', 'size', 'color'])->get() ?? [];
        $this->subtotal = $cart?->items()->sum('subtotal') ?? 0; // Fix: Sum subtotal of all cart items
    }

    private function getCart()
    {
        return Auth::check()
            ? Cart::where('user_id', Auth::id())->with('items')->first()
            : Cart::where('session_id', Session::getId())->with('items')->first();
    }

    public function removeFromCart($itemId)
    {
        $cartItem = CartItem::find($itemId);
        if ($cartItem) {
            $cartItem->delete();
            $this->updateCart();
            $this->dispatch('cartUpdated');
        }
    }

    public function render()
    {
        return view('livewire.cart.cart-icon');
    }
}
