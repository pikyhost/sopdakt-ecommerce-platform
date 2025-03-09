<?php

namespace App\Livewire;

use App\Enums\BundleType;
use App\Models\Color;
use App\Models\ProductColor;
use App\Models\ProductColorSize;
use Illuminate\Support\Facades\Log;
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

        $this->selections = [];
        $this->colors = [];
        $this->sizes = [];

        foreach ($this->selectedBundle->products as $bundleProduct) {
            $colorIds = ProductColor::where('product_id', $bundleProduct->id)->pluck('color_id')->unique();
            $colors = Color::whereIn('id', $colorIds)->get();

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

        Log::info('Selections after selectBundle:', ['selections' => $this->selections]);
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
            if (!empty($this->colors[$bundleProduct->id])) {
                foreach ($this->selections[$bundleProduct->id] ?? [] as $index => $selection) {
                    $rules["selections.{$bundleProduct->id}.{$index}.color_id"] = 'required';
                    $rules["selections.{$bundleProduct->id}.{$index}.size_id"] = 'required';
                }
            }
        }
        return $rules;
    }

    protected $messages = [
        'selections.*.*.color_id.required' => 'The color selection is required.',
        'selections.*.*.size_id.required' => 'The size selection is required.',
    ];

    protected $validationAttributes = [
        'selections.*.*.color_id' => 'color',
        'selections.*.*.size_id' => 'size',
    ];


    public function addToCart()
    {
        $this->validate();
        if (!$this->selectedBundle || ($this->product->quantity ?? 0) <= 0) {
            $this->addError('cart_error', 'This product is out of stock!');
            return;
        }

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
