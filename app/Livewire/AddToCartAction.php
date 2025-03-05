<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductColorSize;
use App\Models\Size;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AddToCartAction extends Component
{
    public $product;
    public ?int $sizeId = null;
    public ?int $colorId = null;
    public $quantity = 1;
    public $showModal = false;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function addToCart()
    {
        // Validate input
        $this->validate([
            'sizeId' => 'required',
            'colorId' => 'required',
            'quantity' => 'required|integer|min:1',
        ], [
            'sizeId.required' => 'Please select a size.',
            'colorId.required' => 'Please select a color.',
            'quantity.required' => 'Please enter a quantity.',
            'quantity.integer' => 'Quantity must be a valid number.',
            'quantity.min' => 'Quantity must be at least 1.',
        ]);

        $user = Auth::user();
        $sessionId = session()->getId();
        $availableStock = $this->product->quantity;

        // Stock validation
        if ($availableStock <= 0) {
            $this->addError('cart_error', 'This product is out of stock!');
            return;
        }

        if ($this->quantity > $availableStock) {
            $this->addError('cart_error', 'The requested quantity exceeds available stock!');
            return;
        }

        // Find or create cart
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id ?? null,
            'session_id' => $user ? null : $sessionId
        ]);

        // Check if the item exists in the cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $this->product->id)
            ->where('size_id', $this->sizeId)
            ->where('color_id', $this->colorId)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $this->quantity;

            if ($newQuantity > $availableStock) {
                $this->addError('cart_error', 'Not enough stock available for the requested quantity!');
                return;
            }

            $cartItem->update([
                'quantity' => $newQuantity,
                'subtotal' => $newQuantity * $this->product->discount_price_for_current_country,
            ]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $this->product->id,
                'size_id' => $this->sizeId,
                'color_id' => $this->colorId,
                'quantity' => $this->quantity,
                'price_per_unit' => $this->product->discount_price_for_current_country,
                'subtotal' => $this->quantity * $this->product->discount_price_for_current_country,
            ]);
        }

        // Reduce product stock
        $this->product->decrement('quantity', $this->quantity);

        session()->flash('cart_success', 'Product added to cart successfully!');
        $this->closeModal();
    }

    public function render()
    {
        // Get available colors for the product
        $colors = ProductColor::where('product_id', $this->product->id)->pluck('color_id')->unique();

        // Get available sizes only if a color is selected
        $sizes = collect();
        if ($this->colorId) {
            $sizes = ProductColorSize::whereHas('productColor', function ($query) {
                $query->where('product_id', $this->product->id)
                    ->where('color_id', $this->colorId);
            })
                ->pluck('size_id')
                ->unique();
        }
        return view('livewire.add-to-cart-action', [
            'colors' => Color::whereIn('id', $colors)->get(),
            'sizes' => Size::whereIn('id', $sizes)->get(),
        ]);
    }
}
