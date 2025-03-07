<?php

namespace App\Livewire;

use App\Enums\BundleType;
use App\Models\Color;
use App\Models\ProductColor;
use App\Models\ProductColorSize;
use Livewire\Component;
use App\Models\Bundle;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddBundleToCart extends Component
{
    public $product;
    public $selectedBundle;
    public $sizes = [];
    public $colors = [];
    public $selections = []; // Store dynamic selections
    public $showModal = false;

    protected $listeners = ['selectBundle'];

    public function mount($product)
    {
        $this->product = $product;
    }
    public function selectBundle($bundleId)
    {
        $this->selectedBundle = Bundle::with('products')->find($bundleId);

        if (!$this->selectedBundle) {
            return;
        }

        $this->selections = []; // Reset selections

        foreach ($this->selectedBundle->products as $bundleProduct) {
            // Get available colors
            $colorIds = ProductColor::where('product_id', $bundleProduct->id)->pluck('color_id')->unique();
            $this->colors[$bundleProduct->id] = Color::whereIn('id', $colorIds)->get();

            // Fix totalInputs calculation for buy_x_get_y bundles
            $totalInputs = 1; // Default for 'fixed_price'
            if ($this->selectedBundle->bundle_type === BundleType::BUY_X_GET_Y) {
                $totalInputs = (int) $this->selectedBundle->buy_x + (int) $this->selectedBundle->get_y;
            }

            for ($i = 0; $i < $totalInputs; $i++) {
                $this->selections[$bundleProduct->id][$i] = [
                    'color_id' => null,
                    'size_id' => null,
                ];
            }
        }

        $this->showModal = true;
    }

    public function updatedSelections($value, $key)
    {
        $keys = explode('.', $key);

        if (count($keys) >= 2) {
            $productId = $keys[0];
            $field = last($keys);

            if ($field === 'color_id') {
                $selectedColorId = $value;

                // Fetch sizes dynamically using ProductColorSize
                $sizes = ProductColorSize::whereHas('productColor', function ($query) use ($productId, $selectedColorId) {
                    $query->where('product_id', $productId)->where('color_id', $selectedColorId);
                })->with('size')->get();

                // Assign sizes
                $this->sizes[$productId] = $sizes->isNotEmpty() ? $sizes->pluck('size') : collect();
            }
        }
    }

    public function addToCart()
    {
        if (!$this->selectedBundle) {
            return;
        }

        $availableStock = $this->product->quantity;

        // Stock validation
        if ($availableStock <= 0) {
            $this->addError('cart_error', 'This product is out of stock!');
            return;
        }

        DB::transaction(function () {
            $cart = Auth::check()
                ? Cart::firstOrCreate(['user_id' => Auth::id()])
                : Cart::firstOrCreate(['session_id' => session()->getId()]);

            $groupedItems = [];
            $totalDiscountPrice = (float) $this->selectedBundle->discount_price; // Bundle discount price
            $isFirstItem = true; // Flag to assign price only to the first item

            foreach ($this->selectedBundle->products as $bundleProduct) {
                foreach ($this->selections[$bundleProduct->id] as $selection) {
                    $key = $bundleProduct->id . '-' . ($selection['color_id'] ?? 'null') . '-' . ($selection['size_id'] ?? 'null');

                    if (isset($groupedItems[$key])) {
                        $groupedItems[$key]['quantity']++;
                    } else {
                        $groupedItems[$key] = [
                            'cart_id' => $cart->id,
                            'product_id' => $bundleProduct->id,
                            'bundle_id' => $this->selectedBundle->id,
                            'size_id' => $selection['size_id'] ?? null,
                            'color_id' => $selection['color_id'] ?? null,
                            'quantity' => 1,
                            'price_per_unit' => $isFirstItem ? $totalDiscountPrice : 0, // First item gets total bundle price
                            'subtotal' => $isFirstItem ? $totalDiscountPrice : 0, // First item stores subtotal
                        ];
                        $isFirstItem = false; // Ensure only one item holds the bundle price
                    }
                }
            }

            // Insert grouped items into the cart
            foreach ($groupedItems as $item) {
                CartItem::create($item);
            }
        });

        $this->showModal = false;
        $this->dispatch('cartUpdated');
    }


    public function render()
    {
        return view('livewire.add-bundle-to-cart');
    }
}
