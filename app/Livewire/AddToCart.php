<?php

namespace App\Livewire;

use App\Models\Color;
use App\Models\ProductColor;
use App\Models\ProductColorSize;
use App\Models\Size;
use Livewire\Component;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AddToCart extends Component
{
    public int $productId;
    public int $quantity = 1;
    public int $cartTotalQuantity = 0;
    public int $productCartQuantity = 0;
    public array $cartItems = [];
    public ?int $sizeId = null;
    public ?int $colorId = null;
    public bool $isDisabled = true; // Button disable state

    protected ?Cart $cart = null;

    public function rules()
    {
        return [
            'colorId' => [
                function ($attribute, $value, $fail) {
                    if (\App\Models\Product::find($this->productId)->productColors->isNotEmpty() && empty($value)) {
                        $fail('The color selection is required.');
                    }
                }
            ],

            'sizeId' => ['required_with:colorId', 'exists:sizes,id'],

            'quantity' => 'required|integer|min:1'
        ];
    }

    public function mount(int $productId): void
    {
        $this->productId = $productId;
        $this->loadCart();
        $this->loadCartData();
        $this->updateButtonState();
    }

    private function loadCart(): void
    {
        $this->cart = Cart::where(function ($query) {
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', Session::getId());
            }
        })->with('items.product')->first();
    }

    private function loadCartData(): void
    {
        if (!$this->cart) {
            $this->cartTotalQuantity = 0;
            $this->productCartQuantity = 0;
            $this->cartItems = [];
            return;
        }

        $this->cartTotalQuantity = $this->cart->items->sum('quantity');
        $this->productCartQuantity = $this->cart->items
            ->where('product_id', $this->productId)
            ->sum('quantity');

        $this->cartItems = $this->cart->items->toArray();
    }

    public function updated($property): void
    {
        if (in_array($property, ['sizeId', 'colorId'])) {
            $this->updateButtonState();
        }
    }

    private function updateButtonState(): void
    {
        $this->isDisabled = empty($this->sizeId) || empty($this->colorId);
    }

    public function addToCart(): void
    {
        $this->validate();

        $product = Product::findOrFail($this->productId);

        $availableStock = $product->quantity;

        // Stock validation
        if ($availableStock <= 0) {
            $this->addError('cart_error', 'This product is out of stock!');
            return;
        }

        if ($this->quantity > $availableStock) {
            $this->addError('cart_error', 'The requested quantity exceeds available stock!');
            return;
        }

        // Ensure only one cart per user or session
        $this->cart = Cart::firstOrCreate(
            ['user_id' => Auth::id(), 'session_id' => Auth::check() ? null : Session::getId()]
        );

        // Check if the product with the same size and color already exists in the cart
        $cartItem = $this->cart->items()
            ->where('product_id', $product->id)
            ->where('size_id', $this->sizeId)
            ->where('color_id', $this->colorId)
            ->first();

        if ($cartItem) {
            // Update quantity and subtotal if item already exists
            $cartItem->increment('quantity', $this->quantity);
            $cartItem->update(['subtotal' => $cartItem->quantity * $cartItem->price_per_unit]);
        } else {
            // Create a new cart item if not exists
            $this->cart->items()->create([
                'product_id' => $product->id,
                'size_id' => $this->sizeId,
                'color_id' => $this->colorId,
                'quantity' => $this->quantity,
                'price_per_unit' => (float) $product->discount_price_for_current_country,
                'subtotal' => $this->quantity * (float) $product->discount_price_for_current_country,
            ]);
        }

        // Refresh cart details
        $this->loadCartData();
        $this->dispatch('cartUpdated');
        session()->flash('success', 'Product added to cart!');
    }


    public function render()
    {
        // Get available colors for the product
        $colors = ProductColor::where('product_id', $this->productId)->pluck('color_id')->unique();

        // Get available sizes only if a color is selected
        $sizes = collect();
        if ($this->colorId) {
            $sizes = ProductColorSize::whereHas('productColor', function ($query) {
                $query->where('product_id', $this->productId)
                    ->where('color_id', $this->colorId);
            })
                ->pluck('size_id')
                ->unique();
        }

        return view('livewire.add-to-cart', [
            'colors' => Color::whereIn('id', $colors)->get(),
            'sizes' => Size::whereIn('id', $sizes)->get(),
        ]);
    }
}
