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

    protected $listeners = ['refreshCart' => 'loadCart'];

    public function mount()
    {
        $this->currentRoute = Route::currentRouteName();
        $this->loadCart();
        $this->loadCountries();

        if (Setting::isShippingEnabled()) {
            $this->loadShippingTypes();
        }

        if (auth()->guest()) {
            // Check for guest contact details
            $contact = Contact::where('session_id', session()->getId())->first();
            if ($contact) {
                $this->country_id = $contact->country_id ?? GeneralHelper::getCountryId();
                $this->governorate_id = $contact->governorate_id;
                $this->city_id = $contact->city_id;
            } else {
                // If no contact record, set country based on IP
                $this->country_id = GeneralHelper::getCountryId();
            }
        } else {
            // Retrieve the user's primary address or fallback to the first available address
            $user = auth()->user();
            $primaryAddress = $user->addresses()->where('is_primary', true)->first();
            $firstAddress = $user->addresses()->first(); // Get the first available address if no primary exists

            $address = $primaryAddress ?? $firstAddress; // Use primary if available, otherwise fallback to first

            if ($address) {
                $this->country_id = $address->country_id;
                $this->governorate_id = $address->governorate_id;
                $this->city_id = $address->city_id;
            } else {
                // If no address exists, set country based on IP
                $this->country_id = GeneralHelper::getCountryId();
            }
        }

        // Load dependent dropdowns
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

        // Load shipping info into Livewire properties only if shipping locations are enabled
        if (Setting::isShippingLocationsEnabled()) {
            $this->country_id = $this->cart->country_id;
            $this->governorate_id = $this->cart->governorate_id;
            $this->city_id = $this->cart->city_id;

            // Ensure dependent dropdowns are populated
            $this->governorates = $this->country_id ? Governorate::where('country_id', $this->country_id)->get() : [];
            $this->cities = $this->governorate_id ? City::where('governorate_id', $this->governorate_id)->get() : [];
        }

        $this->selected_shipping = $this->cart->shipping_type_id;

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

    public function updateCartShipping()
    {
        if ($this->cart) {
            $this->cart->update([
                'shipping_type_id' => $this->selected_shipping,
                'country_id' => Setting::isShippingLocationsEnabled() ? $this->country_id : null,
                'governorate_id' => Setting::isShippingLocationsEnabled() ? $this->governorate_id : null,
                'city_id' => Setting::isShippingLocationsEnabled() ? $this->city_id : null,
            ]);
        }
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

    public function proceedToCheckout()
    {
        $this->validate([
                'selected_shipping' => Setting::isShippingEnabled() ? 'required' : 'nullable',
            ] +  [
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
                'shipping_type_id' => $this->selected_shipping ?? null,
                'shipping_cost' => $this->shippingCost,
            ]);
        }

        return redirect()->route('checkout.index');
    }

    public function getIsCheckoutReadyProperty()
    {
        $isShippingEnabled = Setting::getSetting('shipping_type_enabled') ?? true;
        $isShippingLocationEnabled = Setting::getSetting('shipping_location_enabled') ?? false;

        return $this->cart &&
            ($isShippingEnabled ? $this->cart->shipping_type_id : true) &&
            ($isShippingLocationEnabled ? ($this->cart->country_id && $this->cart->governorate_id) : true) &&
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
            'currentRoute' => $this->currentRoute,
        ]);
    }
}
