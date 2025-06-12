<?php

namespace App\Livewire;

use App\Helpers\GeneralHelper;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Contact;
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

    protected $listeners = ['refreshCart' => 'loadCart', 'productAdded' => 'refreshCart', 'cartUpdated' => 'loadCart'];

    public function refreshCart()
    {
        $this->loadCart();
    }

    public function mount()
    {//...
    }


    public function loadCart()
    {
     ///.....
        $this->calculateTotals();
    }

    public function loadCountries()
    {
        $this->countries = Country::all();
    }


    public function loadShippingTypes()
    {
        $this->shipping_types = ShippingType::where('status', true)->get();
    }

    public function updatedCountryId()
    {
        $this->country_id = $this->country_id ?: null;

        // Reset governorates and cities only if shipping locations are enabled
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

    public function updatedSelectedShipping()
    {
        // Get the base shipping type cost
        $shippingTypeCost = 0.0;

        if ($shippingType = ShippingType::find($this->selected_shipping)) {
            $shippingTypeCost = $shippingType->cost;
        }

        // If the cart has only one item and that product has free shipping, set cost to 0
        if (count($this->cartItems) === 1) {
            $cartItem = $this->cartItems[0];
            $product = Product::find($cartItem['product']['id']);
            if ($product && $product->is_free_shipping) {
                $shippingTypeCost = 0.0;
            }
        }

        // Location-based shipping costs only if shipping locations are enabled
        $locationBasedShippingCosts = [];

        if (Setting::isShippingLocationsEnabled()) {
            foreach ($this->cartItems as $cartItem) {
                $product = Product::find($cartItem['product']['id']);

                if (!$product) {
                    continue;
                }

                // Get the shipping cost per product
                $productShippingCost = $this->calculateProductShippingCost($product);
                $locationBasedShippingCosts[] = $productShippingCost;
            }
        }

        // Get the highest shipping cost among products and shipping type
        $locationBasedShippingCost = !empty($locationBasedShippingCosts)
            ? max($locationBasedShippingCosts)
            : 0.0;

        $this->shippingCost = max($shippingTypeCost, $locationBasedShippingCost);

        $this->calculateTotals();
    }


    /**
     * Retrieve the product's shipping cost.
     * If the product has free shipping, it returns 0.
     */
    private function getProductShippingCost(Product $product): ?float
    {
        // Return 0 if free shipping is enabled for the product.
        if ($product->is_free_shipping) {
            return 0.0;
        }

        // If shipping locations are disabled, return null
        if (!Setting::isShippingLocationsEnabled()) {
            return null;
        }

        $shippingCosts = $product->shippingCosts()->get();

        // Step 1: Check City Shipping Cost (If Selected)
        if (!empty($this->city_id)) {
            $cityCost = $shippingCosts->where('city_id', $this->city_id)->first();
            if ($cityCost) {
                return $cityCost->cost;
            }
        }

        // Step 2: Check Governorate Shipping Cost (If Selected)
        if (!empty($this->governorate_id)) {
            $governorateCost = $shippingCosts
                ->where('governorate_id', $this->governorate_id)
                ->whereNull('city_id') // Ensure it's not a city-specific record
                ->first();

            if ($governorateCost) {
                return $governorateCost->cost;
            }
        }

        // Step 3: Check Country Shipping Cost (If Selected)
        if (!empty($this->country_id)) {
            $countryCost = $shippingCosts
                ->where('country_id', $this->country_id)
                ->whereNull('governorate_id') // Ensure it's not a governorate-specific record
                ->whereNull('city_id') // Ensure it's not a city-specific record
                ->first();

            if ($countryCost) {
                return $countryCost->cost;
            }
        }

        return null; // No matching shipping cost found
    }

    /**
     * Ensures a float is returned for the product shipping cost.
     */
    private function calculateProductShippingCost(Product $product): float
    {
        return $this->getProductShippingCost($product) ?? 0.0;
    }

    /**
     * Check if the governorate belongs to a shipping zone that has a cost in the product's shippingCosts().
     */
    private function getZoneShippingCostFromProduct($shippingCosts, $governorateId): ?float
    {
        if (!Setting::isShippingLocationsEnabled() || !$governorateId) {
            return null;
        }

        $governorate = Governorate::find($governorateId);
        if (!$governorate) {
            return null;
        }

        // Check if this governorate belongs to a shipping zone.
        $zone = $governorate->shippingZones()->first();
        if (!$zone) {
            return null;
        }

        // Return the shipping cost from the zone.
        return $shippingCosts->firstWhere('shipping_zone_id', $zone->id)?->cost;
    }

    /**
     * Get fallback location-based cost (City → Governorate → Zone → Country).
     */
    private function getFallbackLocationBasedCost(): float
    {
        if (!Setting::isShippingLocationsEnabled()) {
            return 0.0;
        }

        if ($this->city_id) {
            $cityCost = City::where('id', $this->city_id)->value('cost');
            if (!is_null($cityCost) && $cityCost > 0) {
                return $cityCost;
            }
        }

        if ($this->governorate_id) {
            $governorateCost = Governorate::where('id', $this->governorate_id)->value('cost');
            if (!is_null($governorateCost) && $governorateCost > 0) {
                return $governorateCost;
            }

            // If no governorate cost, check its related shipping zone.
            $zoneCost = Governorate::find($this->governorate_id)?->shippingZones()->pluck('cost')->first();
            if (!is_null($zoneCost) && $zoneCost > 0) {
                return $zoneCost;
            }
        }

        if ($this->country_id) {
            $countryCost = Country::where('id', $this->country_id)->value('cost');
            if (!is_null($countryCost) && $countryCost > 0) {
                return $countryCost;
            }
        }

        return 0.0;
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        $seenBundles = [];
        $locationBasedShippingCosts = [];

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

            // Get shipping cost for each product/bundle
            if (isset($item['product']['id']) && !empty($item['product']['id'])) {
                $product = Product::find($item['product']['id']);
                if ($product) {
                    $locationBasedShippingCosts[] = $this->calculateProductShippingCost($product);
                }
            } elseif (isset($item['bundle']['id']) && !empty($item['bundle']['id'])) {
                // Get all products in the bundle and check their shipping costs
                $bundleProducts = Product::where('bundle_id', $item['bundle']['id'])->get();
                foreach ($bundleProducts as $product) {
                    $locationBasedShippingCosts[] = $this->calculateProductShippingCost($product);
                }
            }
        }

        // Determine if location-based shipping is enabled
        $isShippingLocationEnabled = Setting::getSetting('shipping_location_enabled') ?? false;

        // Get the highest shipping cost among the products if location-based shipping is enabled
        $locationBasedShippingCost = ($isShippingLocationEnabled && !empty($locationBasedShippingCosts))
            ? max($locationBasedShippingCosts)
            : 0.0;

        // Calculate shipping type cost
        $shippingTypeCost = $this->selected_shipping
            ? ShippingType::find($this->selected_shipping)?->shipping_cost ?? 0.0
            : 0.0;

        // Assign total shipping cost as the maximum of the two
        $this->shippingCost = max($shippingTypeCost, $locationBasedShippingCost);

        // Retrieve tax percentage from settings
        $taxPercentage = Setting::first()?->tax_percentage ?? 0;

        // Calculate tax if applicable
        $this->tax = ($taxPercentage > 0) ? ($this->subtotal * $taxPercentage / 100) : 0;

        // Final total: subtotal + highest shipping + tax
        $this->total = $this->subtotal + $this->shippingCost + $this->tax;
    }

    public function render()
    {
        $cartItems = collect($this->cartItems);

        // Extract product IDs safely
        $productIds = $cartItems->pluck('product.id')->filter()->unique();

        // Get complementary product IDs directly from the relationship
        $complementaryProductIds = Product::whereIn('id', $productIds)
            ->with('complementaryProducts:id') // Only fetch IDs
            ->get()
            ->pluck('complementaryProducts.*.id') // Get nested IDs
            ->flatten()
            ->unique();

        // Fetch the actual complementary products (excluding cart items)
        $complementaryProducts = Product::whereIn('id', $complementaryProductIds)
            ->whereNotIn('id', $productIds) // Exclude cart items
            ->inRandomOrder()
            ->limit(6)
            ->get();

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
            'currentRoute' => $this->currentRoute,
            'complementaryProducts' => $complementaryProducts,
        ]);
    }
}
