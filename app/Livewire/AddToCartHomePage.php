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

class AddToCartHomePage extends Component
{
    public $product;
    public ?int $sizeId = null;
    public ?int $colorId = null;
    public $quantity= 1;
    public bool $showModal = false;

    public function mount(Product $product)
    {
        $this->product = $product;
    }
    public function increaseQuantity()
    {
        $this->quantity++;
    }

    public function decreaseQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
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
        // Clear previous errors
        $this->resetErrorBag();

        if (!$this->quantity || $this->quantity < 1) {
            $this->addError('quantity', 'Please enter a valid quantity.');
            return;
        }

        $product = $this->product;

        // Check if the product has colors
        $hasColors = $product->productColors()->exists();

        // If the product has colors, ensure color is selected
        if ($hasColors && !$this->colorId) {
            $this->addError('colorId', 'Please select a color.');
            return;
        }

        // Check if the selected color has available sizes
        if ($this->colorId) {
            $color = $product->productColors()->where('color_id', $this->colorId)->first();
            $hasSizes = $color && $color->sizes()->exists();

            // If the selected color has sizes, ensure size is selected
            if ($hasSizes && !$this->sizeId) {
                $this->addError('sizeId', 'Please select a size.');
                return;
            }
        }

        // Stock and quantity validation
        $availableStock = $product->quantity;
        if ($availableStock <= 0) {
            $this->addError('cart_error', 'This product is out of stock!');
            return;
        }

        if ($this->quantity > $availableStock) {
            $this->addError('cart_error', 'The requested quantity exceeds available stock!');
            return;
        }

        $user = Auth::user();
        $sessionId = session()->getId();
        $pricePerUnit = (float) $this->product->discount_price_for_current_country; // Ensure valid decimal

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
                'subtotal' => $newQuantity * $pricePerUnit,
            ]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $this->product->id,
                'size_id' => $this->sizeId,
                'color_id' => $this->colorId,
                'quantity' => $this->quantity,
                'price_per_unit' => $pricePerUnit, // Ensure it's a float
                'subtotal' => $this->quantity * $pricePerUnit,
            ]);
        }

        // Reduce product stock safely
        $this->product->decrement('quantity', $this->quantity);

        session()->flash('cart_success', 'Product added to cart successfully!');
        $this->closeModal();
        $this->dispatch('cartUpdated');
        $this->dispatch('productAdded', $this->product->id);

    }

    private function availableSizes()
    {
        return $this->product->productColors->where('id', $this->colorId)->first()?->sizes ?? [];
    }

    public function render()
    {
        $colors = ProductColor::where('product_id', $this->product->id)->pluck('color_id')->unique();
        $sizes = collect();

        if ($this->colorId) {
            $sizes = ProductColorSize::whereHas('productColor', function ($query) {
                $query->where('product_id', $this->product->id)
                    ->where('color_id', $this->colorId);
            })
                ->pluck('size_id')
                ->unique();
        }

        return view('livewire.add-to-cart-home-page', [
            'colors' => Color::whereIn('id', $colors)->get(),
            'sizes' => Size::whereIn('id', $sizes)->get(),
        ]);
    }
}
