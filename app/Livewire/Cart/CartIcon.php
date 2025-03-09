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

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $cart = $this->getCart();

        $this->cartCount = $cart?->items()->sum('quantity') ?? 0;
        $this->cartItems = $cart?->items()->with(['product', 'size', 'color'])->get() ?? [];
        $this->subtotal = $cart?->items()->sum('subtotal') ?? 0;
    }

    private function getCart()
    {
        return Auth::check()
            ? Cart::where('user_id', Auth::id())->with('items')->first()
            : Cart::where('session_id', Session::getId())->with('items')->first();
    }

    public function removeCartItem($id)
    {
        $cart = $this->getCart();

        if (!$cart) {
            return;
        }

        $cartItem = CartItem::where('cart_id', $cart->id)->find($id);

        if (!$cartItem) {
            return;
        }

        if ($cartItem->bundle_id) {
            // Remove all cart items with the same bundle_id
            CartItem::where('bundle_id', $cartItem->bundle_id)->delete();
        } else {
            // Remove only this cart item if it's not part of a bundle
            $cartItem->delete();
        }

        $this->loadCart(); // Refresh the cart items
        $this->dispatch('cartUpdated'); // Notify frontend of the update
    }

    public function render()
    {
        return view('livewire.cart.cart-icon');
    }
}
