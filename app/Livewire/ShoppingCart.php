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
        $this->currentRoute = Route::currentRouteName(); // Set route in mount
        $this->loadCart();
        $this->loadCountries();
        $this->loadShippingTypes();

        // Load dependent dropdowns if values exist
        $this->governorates = $this->country_id ? Governorate::where('country_id', $this->country_id)->get() : [];
        $this->cities = $this->governorate_id ? City::where('governorate_id', $this->governorate_id)->get() : [];
    }

    private function extractPrice($priceString)
    {
        return (float) preg_replace('/[^0-9.]/', '', $priceString); // Extract numeric value
    }

    private function extractCurrency($priceString)
    {
        return preg_replace('/[\d.]/', '', trim($priceString)); // Extract currency
    }

    private function calculateSubtotal($priceString, $quantity)
    {
        $price = $this->extractPrice($priceString);
        $currency = $this->extractCurrency($priceString);
        return number_format($price * $quantity, 2) . ' ' . $currency;
    }



    public function loadCart()
    {
        if (auth()->check()) {
            $this->cart = Cart::firstOrCreate(['user_id' => auth()->id()], ['session_id' => null]);
        } else {
            $session_id = Session::get('cart_session', Session::getId());
            Session::put('cart_session', $session_id);

            $this->cart = Cart::firstOrCreate(['session_id' => $session_id], ['user_id' => null]);
        }

        // Load shipping info into Livewire properties
        $this->selected_shipping = $this->cart->shipping_type_id;
        $this->country_id = $this->cart->country_id;
        $this->governorate_id = $this->cart->governorate_id;
        $this->city_id = $this->cart->city_id;

        // Ensure dependent dropdowns are populated
        $this->governorates = $this->country_id ? Governorate::where('country_id', $this->country_id)->get() : [];
        $this->cities = $this->governorate_id ? City::where('governorate_id', $this->governorate_id)->get() : [];

        $this->cartItems = CartItem::where('cart_id', $this->cart->id)
            ->with(['product', 'bundle', 'size', 'color']) // Load all related models
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'price_per_unit' => $this->extractPrice($item->product ? $item->product->discount_price_for_current_country : '0 USD'),
                'subtotal' => $this->calculateSubtotal(
                    $item->product ? $item->product->discount_price_for_current_country : '0 USD',
                    $item->quantity
                ),
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
                'size' => $item->size ? [
                    'id' => $item->size->id,
                    'name' => $item->size->name,
                ] : null,
                'color' => $item->color ? [
                    'id' => $item->color->id,
                    'name' => $item->color->name,
                    'code' => $item->color->code, // Assuming colors have a hex code
                ] : null,
            ])
            ->toArray();

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

        // Get price string from the product
        $priceString = $cartItem->product ? $cartItem->product->discount_price_for_current_country : '0 USD';

        // Extract price and currency separately
        $price = $this->extractPrice($priceString);
        $currency = $this->extractCurrency($priceString);

        // Calculate subtotal (only numeric value for DB)
        $subtotal = $price * $cartItem->quantity;

        $cartItem->update([
            'subtotal' => $subtotal,  // Store numeric value only
        ]);

        // Convert cart items to a collection, update only the changed item
        $this->cartItems = collect($this->cartItems)->map(function ($item) use ($cartItem, $subtotal, $currency) {
            if ($item['id'] === $cartItem->id) {
                $item['quantity'] = $cartItem->quantity;
                $item['subtotal'] = number_format($subtotal, 2) . ' ' . $currency; // Format for display
                $item['currency'] = $currency;
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
        // Fetch all related shipping costs for the product
        $shippingCosts = $product->shippingCosts()->get();

        // 🔹 Step 1: Check Shipping Costs from the Product’s `shippingCosts()`
        if ($this->city_id) {
            $cityCost = $shippingCosts->firstWhere('city_id', $this->city_id)?->cost;
            if (!is_null($cityCost)) return $cityCost;
        }

        if ($this->governorate_id) {
            $governorateCost = $shippingCosts->firstWhere('governorate_id', $this->governorate_id)?->cost;
            if (!is_null($governorateCost)) return $governorateCost;

            // 🔹 Check if this governorate belongs to a zone with a shipping cost in `shippingCosts()`
            $zoneCost = $this->getZoneShippingCostFromProduct($shippingCosts, $this->governorate_id);
            if (!is_null($zoneCost)) return $zoneCost;
        }

        if ($this->country_id) {
            $countryCost = $shippingCosts->firstWhere('country_id', $this->country_id)?->cost;
            if (!is_null($countryCost)) return $countryCost;
        }

        // 🔹 Step 2: If No `shippingCosts()` Found, Fallback to Model-Based Costs
        return $this->getFallbackLocationBasedCost() ?? $product->cost ?? 0.0;
    }

    /**
     * Check if the governorate belongs to a shipping zone that has a cost in the product's shippingCosts().
     */
    private function getZoneShippingCostFromProduct($shippingCosts, $governorateId): ?float
    {
        if (!$governorateId) return null;

        $governorate = Governorate::find($governorateId);
        if (!$governorate) return null;

        // 🔹 Check if this governorate belongs to a shipping zone
        $zone = $governorate->shippingZones()->first();
        if (!$zone) return null;

        // 🔹 Check if this shipping zone has a cost inside the product’s `shippingCosts()`
        return $shippingCosts->firstWhere('shipping_zone_id', $zone->id)?->cost;
    }

    /**
     * Get fallback location-based cost (City → Governorate → Zone → Country).
     */
    private function getFallbackLocationBasedCost(): ?float
    {
        if ($this->city_id) {
            $cityCost = City::where('id', $this->city_id)->value('cost');
            if (!is_null($cityCost) && $cityCost > 0) return $cityCost;
        }

        if ($this->governorate_id) {
            $governorateCost = Governorate::where('id', $this->governorate_id)->value('cost');
            if (!is_null($governorateCost) && $governorateCost > 0) return $governorateCost;

            // 🔹 If no governorate cost, check its related shipping zone
            $zoneCost = Governorate::find($this->governorate_id)?->shippingZones()->pluck('cost')->first();
            if (!is_null($zoneCost) && $zoneCost > 0) return $zoneCost;
        }

        if ($this->country_id) {
            $countryCost = Country::where('id', $this->country_id)->value('cost');
            if (!is_null($countryCost) && $countryCost > 0) return $countryCost;
        }

        return null;
    }

    /**
     * Final calculation method.
     */
    private function calculateProductShippingCost(Product $product): float
    {
        return $this->getProductShippingCost($product);
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        $seenBundles = [];
        $locationBasedShippingCost = 0;

        foreach ($this->cartItems as $item) {
            // Calculate subtotal
            if (!empty($item['bundle']['id'])) {
                if (!in_array($item['bundle']['id'], $seenBundles)) {
                    $this->subtotal += $item['quantity'] * (float) $item['price_per_unit'];
                    $seenBundles[] = $item['bundle']['id'];
                }
            } else {
                $this->subtotal += $item['quantity'] * (float) $item['price_per_unit'];
            }

            // Calculate shipping cost
            if (isset($item['product']['id']) && !empty($item['product']['id'])) {
                $product = Product::find($item['product']['id']);
                if ($product) {
                    $locationBasedShippingCost += $this->calculateProductShippingCost($product);
                }
            } elseif (isset($item['bundle']['id']) && !empty($item['bundle']['id'])) {
                // Get all products in the bundle
                $bundleProducts = Product::where('bundle_id', $item['bundle']['id'])->get();
                foreach ($bundleProducts as $product) {
                    $locationBasedShippingCost += $this->calculateProductShippingCost($product);
                }
            }
        }

        // Calculate shipping type cost
        $shippingTypeCost = $this->selected_shipping
            ? ShippingType::find($this->selected_shipping)?->shipping_cost ?? 0.0
            : 0.0;

        // Assign total shipping cost
        $this->shippingCost = max(0, $shippingTypeCost + $locationBasedShippingCost);

        // Retrieve tax percentage from settings
        $taxPercentage = Setting::first()?->tax_percentage ?? 0;

        // Calculate tax if applicable
        $this->tax = ($taxPercentage > 0) ? ($this->subtotal * $taxPercentage / 100) : 0;

        // Final total: subtotal + shipping + tax
        $this->total = $this->subtotal + $this->shippingCost + $this->tax;
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
            'taxPercentage' =>  Setting::first()?->tax_percentage ?? 0,
            'currentRoute' => $this->currentRoute, // Now comes from public property
        ]);
    }
}
