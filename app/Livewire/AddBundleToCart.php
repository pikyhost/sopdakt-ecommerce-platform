<?php

namespace App\Livewire;

use App\Enums\BundleType;
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
    public $selections = [];
    public $showModal = false;

    protected $listeners = ['selectBundle'];

    public function mount($product)
    {
        $this->product = $product;
    }

    public function selectBundle($bundleId)
    {
        $this->selectedBundle = Bundle::with(['products.colors'])->find($bundleId);
        if (!$this->selectedBundle) {
            return;
        }

        $this->selections = [];
        $this->colors = [];
        $this->sizes = [];

        foreach ($this->selectedBundle->products as $bundleProduct) {
            $colors = $bundleProduct->colors;

            if ($colors->isNotEmpty()) {
                $this->colors[$bundleProduct->id] = $colors;
            }

            $totalInputs = ($this->selectedBundle->bundle_type === BundleType::BUY_X_GET_Y)
                ? (int) $this->selectedBundle->buy_x + (int) $this->selectedBundle->get_y
                : 1;

            for ($i = 0; $i < $totalInputs; $i++) {
                $this->selections[$bundleProduct->id][$i] = [
                    'color_id' => $colors->isNotEmpty() ? null : '',
                    'size_id' => $colors->isNotEmpty() ? null : '',
                ];
            }
        }

        $this->showModal = true;
    }

    public function updatedSelections($value, $key)
    {
        $keys = explode('.', $key);
        if (count($keys) >= 3) {
            [$productId, $index, $field] = $keys;

            if ($field === 'color_id' && isset($this->colors[$productId]) && $this->colors[$productId]->isNotEmpty()) {
                $sizes = ProductColorSize::whereHas('productColor', function ($query) use ($productId, $value) {
                    $query->where('product_id', $productId)->where('color_id', $value);
                })->with('size')->get();

                $this->sizes[$productId][$index] = $sizes->map(fn ($item) => $item->size);
            }
        }
    }

    public function rules()
    {
        $rules = [];

        foreach ($this->selectedBundle->products as $bundleProduct) {
            $productId = $bundleProduct->id;

            foreach ($this->selections[$productId] ?? [] as $index => $selection) {
                if (!empty($this->colors[$productId])) {
                    $rules["selections.{$productId}.{$index}.color_id"] = 'required|exists:colors,id';

                    if (!empty($this->sizes[$productId][$index])) {
                        $rules["selections.{$productId}.{$index}.size_id"] = 'required|exists:sizes,id';
                    }
                }
            }
        }

        return $rules;
    }

    protected $messages = [
        'selections.*.*.color_id.required' => 'The color selection is required.',
        'selections.*.*.color_id.exists'   => 'The selected color is invalid.',
        'selections.*.*.size_id.required'  => 'The size selection is required.',
        'selections.*.*.size_id.exists'    => 'The selected size is invalid.',
    ];

    protected $validationAttributes = [
        'selections.*.*.color_id' => 'color',
        'selections.*.*.size_id'  => 'size',
    ];

    public function addToCart()
    {
        if (!$this->selectedBundle) {
            $this->addError('cart_bundle_error', 'Please select a bundle before adding to cart.');
            return;
        }

        if (($this->product->quantity ?? 0) <= 0) {
            $this->addError('cart_bundle_error', 'This product is out of stock!');
            return;
        }

        $totalExpectedInputs = ($this->selectedBundle->bundle_type === BundleType::BUY_X_GET_Y)
            ? (int) $this->selectedBundle->buy_x + (int) $this->selectedBundle->get_y
            : 1;

        foreach ($this->selectedBundle->products as $bundleProduct) {
            $productId = $bundleProduct->id;
            $actualSelections = $this->selections[$productId] ?? [];

            if (count($actualSelections) !== $totalExpectedInputs) {
                $this->addError('cart_bundle_error', "You must select {$totalExpectedInputs} options for product: {$bundleProduct->name}.");
                return;
            }
        }

        $this->validate();

        DB::transaction(function () {
            $cart = Auth::check()
                ? Cart::firstOrCreate(['user_id' => Auth::id()])
                : Cart::firstOrCreate(['session_id' => session()->getId()]);

            $groupedItems = [];
            $totalDiscountPrice = (float) $this->selectedBundle->discount_price;
            $isFirstItem = true;

            foreach ($this->selectedBundle->products as $bundleProduct) {
                foreach ($this->selections[$bundleProduct->id] ?? [] as $selection) {
                    $key = implode('-', [$bundleProduct->id, $selection['color_id'] ?? 'null', $selection['size_id'] ?? 'null']);

                    if (isset($groupedItems[$key])) {
                        $groupedItems[$key]['quantity']++;
                    } else {
                        $groupedItems[$key] = [
                            'cart_id' => $cart->id,
                            'product_id' => $bundleProduct->id,
                            'bundle_id' => $this->selectedBundle->id,
                            'size_id' => $selection['size_id'] ?: null,
                            'color_id' => $selection['color_id'] ?: null,
                            'quantity' => 1,
                            'price_per_unit' => $isFirstItem ? $totalDiscountPrice : 0,
                            'subtotal' => $isFirstItem ? $totalDiscountPrice : 0,
                        ];
                        $isFirstItem = false;
                    }
                }
            }

            CartItem::insert($groupedItems);
        });

        $this->showModal = false;
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.add-bundle-to-cart');
    }
}
