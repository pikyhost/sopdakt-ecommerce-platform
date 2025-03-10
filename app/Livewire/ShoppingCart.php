<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingType;
use App\Services\CartService;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class ShoppingCart extends Component
{
    public $currentRoute;
    public $cartItems = [];
    public $subtotal = 0.0;
    public $total = 0.0;
    public $tax;
    public $shippingCost = 0.0;
    public $cart;
    public $countries = [];
    public $governorates = [];
    public $cities = [];
    public $country_id, $governorate_id, $city_id;
    public $shipping_types = [];
    public $selected_shipping = null;

    protected $listeners = ['refreshCart' => 'loadCart'];

    public function mount()
    {
        $this->currentRoute = Route::currentRouteName();

        $this->loadCart();
        $this->loadCountries();
        $this->loadShippingTypes();

        // Load dependent dropdowns if values exist
        $this->governorates = $this->country_id ? Governorate::where('country_id', $this->country_id)->get() : []; // line 44
        $this->cities = $this->governorate_id ? City::where('governorate_id', $this->governorate_id)->get() : []; // line 55
    }

    public function loadCart()
    {
        $this->cart = CartService::getCart();

        if (!$this->cart) {
            $this->cart = auth()->check()
                ? Cart::create(['user_id' => auth()->id()])
                : Cart::create(['session_id' => Session::get('cart_session', Session::getId())]);
        }

        $this->selected_shipping = $this->cart->shipping_type_id;
        $this->country_id = $this->cart->country_id;
        $this->governorate_id = $this->cart->governorate_id;
        $this->city_id = $this->cart->city_id;

        // Fetch governorates and cities in ONE query instead of multiple queries
        if ($this->country_id) {
            $this->governorates = Governorate::where('country_id', $this->country_id)->with('cities')->get();
            $this->cities = $this->governorates->flatMap->cities;
        } else {
            $this->governorates = [];
            $this->cities = [];
        }

        // Fetch cart items
        $this->cartItems = $this->cart->items->map(fn($item) => [
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
            'bundle' => $item->bundle ? [
                'id' => $item->bundle->id,
                'name' => $item->bundle->name,
                'price' => $item->bundle->discount_price ?? 0,
            ] : null,
            'size' => $item->size ? ['id' => $item->size->id, 'name' => $item->size->name] : null,
            'color' => $item->color ? ['id' => $item->color->id, 'name' => $item->color->name, 'code' => $item->color->code] : null,
        ])->toArray();

        $this->calculateTotals();
    }


    public function loadCountries()
    {
        $this->countries = Country::all();
    }

    public function loadShippingTypes()
    {
        $this->shipping_types = ShippingType::all();
    }

    public function updateCartShipping()
    {
        if ($this->cart) {
            $this->cart->update([
                'shipping_type_id' => $this->selected_shipping,
                'country_id' => $this->country_id,
                'governorate_id' => $this->governorate_id,
                'city_id' => $this->city_id,
            ]);
        }
    }

    public function updatedCountryId()
    {
        $this->country_id = $this->country_id ?: null;

        // Reset governorates and cities
        $this->governorates = $this->country_id ? Governorate::where('country_id', $this->country_id)->get() : [];
        $this->governorate_id = null;
        $this->cities = [];
        $this->city_id = null;

        // Save to cart
        if ($this->cart) {
            $this->cart->update([
                'country_id' => $this->country_id,
                'governorate_id' => null,
                'city_id' => null
            ]);
        }

        $this->calculateTotals();
    }

    public function updatedGovernorateId()
    {
        $this->governorate_id = $this->governorate_id ?: null;

        // Reset cities only if governorate_id is null
        $this->cities = $this->governorate_id ? City::where('governorate_id', $this->governorate_id)->get() : [];
        $this->city_id = null;

        // Save to cart
        if ($this->cart) {
            $this->cart->update([
                'governorate_id' => $this->governorate_id,
                'city_id' => null
            ]);
        }

        $this->calculateTotals();
    }

    public function updatedCityId()
    {
        $this->city_id = $this->city_id ?: null;

        // Save to cart
        if ($this->cart) {
            $this->cart->update([
                'city_id' => $this->city_id,
            ]);
        }

        $this->calculateTotals();
    }

    public function updateQuantity($cartItemId, $action)
    {
        $cartItem = CartItem::find($cartItemId);

        if (!$cartItem) {
            return;
        }

        if ($action === 'increase') {
            $cartItem->increment('quantity');
        } elseif ($action === 'decrease' && $cartItem->quantity > 1) {
            $cartItem->decrement('quantity');
        } else {
            $cartItem->delete();
            $this->loadCart(); // Reload cart to remove deleted item
            return;
        }

        // Update subtotal dynamically
        $cartItem->update([
            'subtotal' => $cartItem->quantity * $cartItem->price_per_unit
        ]);

        // Convert cart items to a collection, update only the changed item
        $this->cartItems = collect($this->cartItems)->map(function ($item) use ($cartItem) {
            if ($item['id'] === $cartItem->id) {
                $item['quantity'] = $cartItem->quantity;
                $item['subtotal'] = $cartItem->subtotal;
            }
            return $item;
        })->toArray(); // Convert back to array for Livewire

        $this->calculateTotals();
        $this->dispatch('cartUpdated');
    }

    public function removeCartItem($id)
    {
        $cartItem =  CartItem::where('cart_id', $this->cart->id)->find($id);

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

    public function updatedSelectedShipping()
    {
        $shippingType = ShippingType::find($this->selected_shipping);
        $this->shippingCost = $shippingType ? $shippingType->cost : 0.0;

        foreach ($this->cartItems as $cartItem) {
            $product = Product::find($cartItem['product']['id']);

            if (!$product) {
                continue;
            }

            // Check product-specific shipping costs
            $productShippingCost = $this->getProductShippingCost($product);

            // If product shipping cost is found, add to total shipping cost
            if ($productShippingCost !== null) {
                $this->shippingCost += $productShippingCost;
            } else {
                // If no product-specific cost, fallback to user-selected location
                $this->shippingCost += $this->getLocationBasedShippingCost();
            }
        }

        $this->calculateTotals();
    }

    private function getProductShippingCost(Product $product)
    {
        // Fetch all related shipping costs
        $shippingCosts = $product->shippingCosts()
//            ->where('shipping_type_id', $this->selected_shipping)
            ->get();

        // Prioritize location: City → Governorate → Shipping Zone → Country
        if ($shippingCosts->where('city_id', $this->city_id)->isNotEmpty()) {
            return $shippingCosts->where('city_id', $this->city_id)->first()->cost;
        }

        if ($shippingCosts->where('governorate_id', $this->governorate_id)->isNotEmpty()) {
            return $shippingCosts->where('governorate_id', $this->governorate_id)->first()->cost;
        }

        if ($shippingCosts->where('shipping_zone_id', $this->cart->shipping_zone_id)->isNotEmpty()) {
            return $shippingCosts->where('shipping_zone_id', $this->cart->shipping_zone_id)->first()->cost;
        }

        if ($shippingCosts->where('country_id', $this->country_id)->isNotEmpty()) {
            return $shippingCosts->where('country_id', $this->country_id)->first()->cost;
        }

        // If product does not have a shipping cost, return the default product shipping cost
        return $product->cost ?? null;
    }

    private function getLocationBasedShippingCost()
    {
        // Check if city has a shipping cost
        if ($this->city_id) {
            $city = City::find($this->city_id);
            if ($city && $city->cost !== null) {
                return $city->cost;
            }
        }

        // If no city, check governorate
        if ($this->governorate_id) {
            $governorate = Governorate::find($this->governorate_id);
            if ($governorate && $governorate->cost !== null) {
                return $governorate->cost;
            }
        }

        // If no governorate, check country
        if ($this->country_id) {
            $country = Country::find($this->country_id);
            if ($country && $country->cost !== null) {
                return $country->cost;
            }
        }

        // Default to 0 if no cost is found
        return 0;
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        $seenBundles = [];
        $locationBasedShippingCost = 0;

        // Fetch Cart once to avoid multiple queries
        static $cart = null;
        if ($cart === null) {
            $cart = CartService::getCart();
        }

        // Fetch all product IDs in one query
        $productIds = collect($this->cartItems)->pluck('product.id')->filter()->toArray();

        // Load Products with Media and Inventory in one query
        $products = Product::whereIn('id', $productIds)
            ->with(['media', 'inventory'])  // Eager load media and inventory
            ->get()
            ->keyBy('id');

        foreach ($this->cartItems as $item) {
            if (!empty($item['bundle']['id'])) {
                if (!in_array($item['bundle']['id'], $seenBundles)) {
                    $this->subtotal += $item['quantity'] * (float) $item['price_per_unit'];
                    $seenBundles[] = $item['bundle']['id'];
                }
            } else {
                $this->subtotal += $item['quantity'] * (float) $item['price_per_unit'];
            }

            // Fetch product details without multiple queries
            if (isset($item['product']['id']) && isset($products[$item['product']['id']])) {
                $locationBasedShippingCost += $this->calculateProductShippingCost($products[$item['product']['id']]);
            }
        }

        // Get tax percentage using the cached function
        $taxPercentage = Setting::getTaxPercentage();

        // Calculate tax
        $this->tax = ($taxPercentage > 0) ? ($this->subtotal * $taxPercentage / 100) : 0;

        // Final total
        $this->total = $this->subtotal + $this->shippingCost + $this->tax;
    }

    /**
     * Calculate the shipping cost for a given product.
     */
    private function calculateProductShippingCost(Product $product): float
    {
        $productShippingCost = $product->getShippingCostByLocation(
            $this->city_id,
            $this->governorate_id,
            $this->country_id
        );

        return $productShippingCost > 0
            ? $productShippingCost
            : $this->getLocationBasedShippingCost();
    }

    public function proceedToCheckout()
    {
        $this->validate([
            'selected_shipping' => 'required',
            'country_id' => 'required|exists:countries,id',
            'governorate_id' => 'required|exists:governorates,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        $taxPercentage = Setting::first()?->tax_percentage ?? 0;
        $taxAmount = ($taxPercentage > 0) ? ($this->subtotal * $taxPercentage / 100) : 0;

        // Ensure the cart exists and update it with the selected shipping and tax info
        if ($this->cart) {
            $this->cart->update([
                'subtotal' => $this->subtotal,
                'total' => $this->total,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'country_id' => $this->country_id,
                'governorate_id' => $this->governorate_id,
                'city_id' => $this->city_id,
                'shipping_type_id' => $this->selected_shipping,
                'shipping_cost' => $this->shippingCost,
            ]);
        }

        return redirect()->route('checkout.index');
    }

    public function getIsCheckoutReadyProperty()
    {
        return $this->cart &&
            $this->cart->shipping_type_id &&
            $this->cart->country_id &&
            $this->cart->governorate_id &&
            $this->cart->subtotal > 0;
    }


    public function render()
    {
        return view('livewire.shopping-cart', [
            'cartItems' => $this->cartItems,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'countries' => $this->countries,
            'governorates' => $this->governorates,
            'cities' => $this->cities,
            'shipping_types' => $this->shipping_types,
            'shippingCost' => $this->shippingCost,
            'taxPercentage' =>  Setting::getTaxPercentage(),
            'currentRoute' => $this->currentRoute, // Now comes from public property
        ]);
    }
}
