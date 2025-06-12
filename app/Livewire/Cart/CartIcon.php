<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use App\Models\CartItem;
use App\Services\CartService;

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
        $cart = CartService::getCart(); // Use cached cart

        if (!$cart) {
            $this->cartCount = 0;
            $this->cartItems = [];
            $this->subtotal = 0;
            return;
        }

        $this->cartCount = $cart->items->sum('quantity');
        $this->subtotal = $cart->items->sum('subtotal');

        $this->cartItems = $cart->items->map(function ($item) {
            return [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'price_per_unit' => $item->price_per_unit,
                'subtotal' => $item->subtotal,
                'product' => $item->product ? [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'feature_product_image_url' => $item->product->getFeatureProductImageUrl() ?? '',
                    'price' => $item->product->discount_price_for_current_country ?? 0,
                ] : null,
                'size' => $item->size ? [
                    'id' => $item->size->id,
                    'name' => $item->size->name,
                ] : null,
                'color' => $item->color ? [
                    'id' => $item->color->id,
                    'name' => $item->color->name,
                    'code' => $item->color->code,
                ] : null,
            ];
        })->toArray();
    }

    public function removeFromCart($itemId)
    {
        $cart = CartService::getCart();

        if (!$cart) {
            return;
        }

        $cartItem = $cart->items->find($itemId);

        if (!$cartItem) {
            return;
        }

        if ($cartItem->bundle_id) {
            CartItem::where('bundle_id', $cartItem->bundle_id)->delete();
        } else {
            $cartItem->delete();
        }

        $cart->refresh(); // Refresh cart after changes
        $this->updateCart();
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.cart.cart-icon');
    }
}
