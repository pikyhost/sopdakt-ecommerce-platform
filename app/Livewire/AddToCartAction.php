<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductColorSize;
use App\Models\Setting;
use App\Models\Size;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AddToCartAction extends Component
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
        $this->resetErrorBag();


        if (!$this->quantity || $this->quantity < 1) {
            $this->addError('quantity', 'Please enter a valid quantity.');
            return;
        }

        if ($this->quantity > 10) {
            $this->addError('quantity', 'The maximum quantity allowed to be added to the cart is 10.');
            return;
        }

        $product = $this->product;
        $hasColors = $product->productColors()->exists();
        $variantStock = null;

        // Color validation
        if ($hasColors) {
            if (!$this->colorId) {
                $this->addError('colorId', 'Please select a color.');
                return;
            }

            $productColor = $product->productColors()->where('color_id', $this->colorId)->first();
            if (!$productColor) {
                $this->addError('colorId', 'Invalid color selection.');
                return;
            }

            // Size validation
            $hasSizes = $productColor->sizes()->exists();
            if ($hasSizes) {
                if (!$this->sizeId) {
                    $this->addError('sizeId', 'Please select a size.');
                    return;
                }

                $productColorSize = $productColor->sizes()->where('size_id', $this->sizeId)->first();
                if (!$productColorSize) {
                    $this->addError('sizeId', 'Invalid size selection.');
                    return;
                }

                $variantStock = $productColorSize->pivot->quantity ?? 0;
            } else {
                $variantStock = $productColor->quantity ?? 0;
            }
        } else {
            $variantStock = $product->quantity ?? 0;
        }

        // Stock validation
        if ($variantStock <= 0) {
            $this->addError('cart_error', 'This product is out of stock!');
            return;
        }

        if ($this->quantity > $variantStock) {
            $this->addError('cart_error', 'The requested quantity exceeds available stock!');
            return;
        }

        // Get user or session-based cart
        $user = Auth::user();
        $sessionId = session()->getId();
        $pricePerUnit = (float) $product->discount_price_for_current_country;

        $cart = Cart::firstOrCreate([
            'user_id' => $user->id ?? null,
            'session_id' => $user ? null : $sessionId
        ]);

        // Check if cart item already exists
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('color_id', $this->colorId)
            ->where('size_id', $this->sizeId)
            ->first();

        $newQuantity = $this->quantity;

        if ($cartItem) {
            $newQuantity += $cartItem->quantity;

            if ($newQuantity > $variantStock) {
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
                'product_id' => $product->id,
                'color_id' => $this->colorId,
                'size_id' => $this->sizeId,
                'quantity' => $this->quantity,
                'price_per_unit' => $pricePerUnit,
                'subtotal' => $this->quantity * $pricePerUnit,
                'currency_id' => optional(Setting::getCurrency())->id,
            ]);
        }

        session()->flash('cart_success', 'Product added to cart successfully!');
        $this->closeModal();
        $this->dispatch('cartUpdated');
    }


    private function availableSizes()
    {
        return $this->product->productColors->where('id', $this->colorId)->first()?->sizes ?? [];
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
