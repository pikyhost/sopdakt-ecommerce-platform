<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\Governorate;
use App\Models\Transaction;
use App\Services\StockLevelNotifier;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Enums\UserRole;
use App\Helpers\GeneralHelper;
use App\Mail\GuestInvitationMail;
use App\Mail\OrderStatusMail;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Invitation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use Spatie\Permission\Models\Role;

class Checkout extends Component
{
    public string|null $paymentUrl = null;
    public $payment_method_id;
    public string $checkoutToken;
    public $currentRoute;
    public $name;
    public $address;
    public $email, $phone, $second_phone, $notes, $create_account = false, $password;
    public $total = 0;
    public $subTotal = 0;
    public $cartItems = [];
    public $shippingCost = 0.0;
    public $taxPercentage;
    public $taxAmount;
    public $cart;

    protected function rules()
    {
        return [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(Auth::id()),
            ],
            'phone' => [
                'required',
                'string',
                'min:10',
                function ($attribute, $value, $fail) {
                    foreach ($this->generatePhoneVariations($value) as $variation) {
                        if (GeneralHelper::isPhoneBlocked($variation)) {
                            $fail(__('validation.blocked_phone', [
                                'link' => '<a href="' . route('contact.us') . '" target="_blank">our support page</a>'
                            ]));
                            break;
                        }
                    }
                },
            ],

            'second_phone' => [
                'required',
                'string',
                'min:10',
                'different:phone',
                function ($attribute, $value, $fail) {
                    foreach ($this->generatePhoneVariations($value) as $variation) {
                        if (GeneralHelper::isPhoneBlocked($variation)) {
                            $fail(__('validation.blocked_phone', [
                                'link' => '<a href="' . route('contact.us') . '" target="_blank">our support page</a>'
                            ]));
                            break;
                        }
                    }
                },
            ],
            'notes' => 'nullable|string',
            'password' => 'nullable|min:6|required_if:create_account,true',
        ];
    }

    protected function generatePhoneVariations(string $input): array
    {
        $digits = preg_replace('/\D/', '', $input); // Remove non-digits

        if (str_starts_with($digits, '0')) {
            $local = $digits;
            $withoutZero = substr($digits, 1);
        } elseif (str_starts_with($digits, '20')) {
            $local = '0' . substr($digits, 2);
            $withoutZero = substr($digits, 2);
        } else {
            $local = '0' . $digits;
            $withoutZero = $digits;
        }

        return array_unique([
            $local,                        // 01025263865
            '20' . $withoutZero,          // 201025263865
            '+20' . $withoutZero,         // +201025263865
        ]);
    }

    public function mount()
    {
        $this->checkoutToken = (string) Str::uuid();

        $this->currentRoute = Route::currentRouteName();

        // Load cart data
        $session_id = session()->getId();
        $this->cart = Auth::check()
            ? Cart::where('user_id', Auth::id())->latest()->first()
            : Cart::where('session_id', $session_id)->latest()->first();

        if (Auth::check()) {
            // Load data for authenticated users
            $user = Auth::user();
            $primaryAddress = $user->addresses()->where('is_primary', true)->first();

            if(!$primaryAddress && $user->addresses()->exists()) {
                $address = auth()->user()->addresses()->first();
            }

            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone;
            $this->second_phone = $user->second_phone;
            $this->address = $primaryAddress->address ?? $address->address?? '';
        } else {
            // Load data for guest users using session_id
            $session_id = session()->getId();
            $guestContact = Contact::where('session_id', $session_id)->first();

            if ($guestContact) {
                $this->name = $guestContact->name;
                $this->email = $guestContact->email;
                $this->phone = $guestContact->phone;
                $this->second_phone = $guestContact->second_phone;
                $this->address = $guestContact->address;
            }
        }

        $this->loadCartItems(); // Ensure cart items are loaded
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

    public function loadCartItems()
    {
        $session_id = session()->getId();

        $cart = Auth::check()
            ? Cart::where('user_id', Auth::id())->latest()->first()
            : Cart::where('session_id', $session_id)->latest()->first();

        if (!$cart) {
            $this->cartItems = [];
            $this->subTotal = 0;
            $this->total = 0;
            $this->shippingCost = 0;
            return;
        }

        $cartItems = CartItem::where('cart_id', $cart->id)
            ->with('product') // Ensure product is loaded
            ->get();

        $this->cartItems = $cartItems->map(function ($item) {
            // Extract price and currency
            $priceString = $item->product ? $item->product->discount_price_for_current_country : '0 USD';
            $price = $this->extractPrice($priceString);
            $currency = $this->extractCurrency($priceString);

            return [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'subtotal' => number_format($item->subtotal, 2) . ' ' . $currency, // Format subtotal for display
                'currency' => $currency,
                'product' => $item->product ? [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => number_format($price, 2) . ' ' . $currency, // Format price for display
                    'feature_product_image_url' => $item->product->getFeatureProductImageUrl() ?? '',
                ] : null,
            ];
        })->toArray();

        // Ensure subtotal is numeric for calculations
        $this->subTotal = (float) $cart->subtotal ?? 0;
        $this->total = (float) $cart->total ?? 0;
        $this->shippingCost = (float) $cart->shipping_cost ?? 0;

        // Tax calculations
        $this->taxPercentage = Setting::first()?->tax_percentage ?? 0;
        $this->taxAmount = ($this->taxPercentage > 0) ? ($this->subTotal * $this->taxPercentage / 100) : 0;
    }

    public function save()
    {
        if (Auth::check()) {
            // Authenticated user - Update their contact info
            $user = Auth::user();

            // Ensure new email is not used by another user
            if ($user->email !== $this->email && User::where('email', $this->email)->exists()) {
                $this->addError('error', __('This email is already in use by another user.'));
                return;
            }

            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'second_phone' => $this->second_phone,
            ]);

            $primaryAddress = $user->addresses()->where('is_primary', true)->first();

            if ($primaryAddress) {
                $primaryAddress->update([
                    'address' => $this->address,
                    'country_id' => $this->cart->country_id ?? null,
                    'governorate_id' => $this->cart->governorate_id ?? null,
                    'city_id' => $this->cart->city_id ?? null,
                ]);
            } else {
                $user->addresses()->create([
                    'address' => $this->address,
                    'address_name' => 'home',
                    'country_id' => $this->cart->country_id ?? null,
                    'governorate_id' => $this->cart->governorate_id ?? null,
                    'city_id' => $this->cart->city_id ?? null,
                    'is_primary' => true,
                ]);
            }

            return $user;
        } else {
            $session_id = session()->getId();
            $guestContact = Contact::where('session_id', $session_id)->first();

            if ($this->create_account) {
                $this->validate(['password' => 'required|min:6']);

                // Create and log in new user
                $user = \App\Models\User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'second_phone' => $this->second_phone,
                    'address' => $this->address,
                    'password' => bcrypt($this->password),
                    'country_id' => $this->cart->country_id,
                    'governorate_id' => $this->cart->governorate_id,
                    'city_id' => $this->cart->city_id,
                ]);

                Auth::login($user);
                if ($guestContact) {
                    $guestContact->delete();
                }

                // Create initial primary address
                $user->addresses()->create([
                    'address' => $this->address,
                    'country_id' => $this->cart->country_id ?? null,
                    'governorate_id' => $this->cart->governorate_id ?? null,
                    'city_id' => $this->cart->city_id ?? null,
                    'address_name' => 'home',
                    'is_primary' => true,
                ]);

                return $user;
            } else {
                // Guest logic
                if (!$guestContact) {
                    $guestContact = Contact::create([
                        'session_id' => $session_id,
                        'name' => $this->name,
                        'email' => $this->email,
                        'phone' => $this->phone,
                        'second_phone' => $this->second_phone,
                        'address' => $this->address,
                        'country_id' => $this->cart->country_id,
                        'governorate_id' => $this->cart->governorate_id,
                        'city_id' => $this->cart->city_id,
                    ]);
                } else {
                    $guestContact->update([
                        'name' => $this->name,
                        'email' => $this->email,
                        'phone' => $this->phone,
                        'second_phone' => $this->second_phone,
                        'address' => $this->address,
                        'country_id' => $this->cart->country_id,
                        'governorate_id' => $this->cart->governorate_id,
                        'city_id' => $this->cart->city_id,
                    ]);
                }
                return $guestContact;
            }
        }
    }

    public function placeOrder()
    {
        try {
            // Block inactive users
            if (Auth::check() && !Auth::user()->is_active) {
                $contactUrl = route('contact.us');
                $this->addError('auth', __('Your account is not active. Please <a href=":url" class="underline text-blue-500 hover:text-blue-700">contact support</a>.', [
                    'url' => $contactUrl
                ]));
                Log::warning('Inactive user tried to place an order', ['user_id' => Auth::id()]);
                return;
            }

            // Prevent double submission
            if (Order::where('checkout_token', $this->checkoutToken)->exists()) {
                $this->addError('duplicate', __('Your order is already being processed. Please wait.'));
                Log::info('Duplicate checkout attempt', ['checkout_token' => $this->checkoutToken]);
                return;
            }

            // Validate input fields
            $this->validate([
                'payment_method_id' => 'nullalbe|in:1,2',
                // Add other fields as needed
            ]);
            Log::info('Validation passed for checkout', ['payment_method_id' => $this->payment_method_id]);

            // Get cart
            $cart = Cart::where(function ($query) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                } else {
                    $query->where('session_id', session()->getId());
                }
            })->with('items')->first();

            if (!$cart || $cart->items->isEmpty()) {
                Log::info('Empty cart during checkout', ['user_id' => Auth::id(), 'session_id' => session()->getId()]);
                return redirect()->route('cart.index')->with('error', __('Your cart is empty.'));
            }

            // Save contact data
            $contact = $this->save();
            Log::info('Contact info saved', ['contact_id' => $contact->id ?? null]);

            // Store checkout session data
            session([
                'pending_checkout' => [
                    'user_id' => Auth::id(),
                    'contact_id' => Auth::check() ? null : $contact->id,
                    'cart_id' => $cart->id,
                    'notes' => $this->notes,
                    'checkout_token' => $this->checkoutToken,
                    'payment_method_id' => $this->payment_method_id,
                ]
            ]);
            Log::info('Checkout session stored', ['checkout_token' => $this->checkoutToken]);

            // If Paymob selected
            if ($this->payment_method_id == 2) {
                try {
                    if (!is_numeric($cart->total)) {
                        Log::error('Invalid cart total', ['total' => $cart->total]);
                        $this->addError('payment', __('Invalid cart total.'));
                        return;
                    }

                    $response = Http::post(url('/api/payment/process'), [
                        'amount_cents' => (int) ($cart->total * 100),
                        'contact_email' => $contact->email ?? Auth::user()?->email,
                        'name' => $contact->name ?? Auth::user()?->name,
                    ]);

                    Log::info('Payment API Response', [
                        'status' => $response->status(),
                        'body' => $response->json(),
                    ]);

                    $data = $response->json();

                    if (isset($data['success']) && $data['success'] === true && isset($data['iframe_url'])) {
                        $this->paymentUrl = $data['iframe_url'];
                        $this->dispatch('payment-url-updated');
                        Log::info('Payment URL generated successfully', ['payment_url' => $this->paymentUrl]);
                        return;
                    }

                    $this->addError('payment', __('Failed to initiate payment. Invalid response.'));
                    Log::error('Payment response invalid', ['response' => $data]);
                    return;

                } catch (\Exception $e) {
                    Log::error('Payment exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                    $this->addError('payment', __('Unexpected error during payment: :msg', [
                        'msg' => $e->getMessage()
                    ]));
                    return;
                }
            }

            // Fallback for other methods like COD
            Log::info('Fallback to manual order creation', ['payment_method_id' => $this->payment_method_id]);
            return $this->createOrderManually($cart, $contact);

        } catch (\Exception $e) {
            Log::critical('Unexpected error in placeOrder', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->addError('fatal', __('An unexpected error occurred. Please try again later.'));
            return;
        }
    }

    public function createOrderManually($cart, $contact = null)
    {
        DB::beginTransaction();

        try {
            $orderData = [
                'payment_method_id' => $this->payment_method_id,
                'user_id' => Auth::id(),
                'shipping_type_id' => $cart->shipping_type_id,
                'coupon_id' => $cart->coupon_id,
                'shipping_cost' => $cart->shipping_cost,
                'country_id' => $cart->country_id,
                'governorate_id' => $cart->governorate_id,
                'city_id' => $cart->city_id,
                'tax_percentage' => $cart->tax_percentage,
                'tax_amount' => $cart->tax_amount,
                'subtotal' => $cart->subtotal,
                'total' => $cart->total,
                'status' => \App\Enums\OrderStatus::Shipping,
                'notes' => $this->notes,
                'checkout_token' => $this->checkoutToken,
            ];

            // Only add contact_id if guest (not authenticated)
            if (!Auth::check() && $contact) {
                $orderData['contact_id'] = $contact->id;
            }

            $order = Order::create($orderData);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'bundle_id' => $item->bundle_id,
                    'size_id' => $item->size_id,
                    'color_id' => $item->color_id,
                    'quantity' => $item->quantity,
                    'price_per_unit' => $item->price_per_unit,
                    'subtotal' => $item->subtotal,
                ]);

                if ($item->product_id) {
                    $product = \App\Models\Product::find($item->product_id);

                    if ($product) {
                        $product->decrement('quantity', $item->quantity);

                        $variant = $product->productColors()
                            ->where('color_id', $item->color_id)
                            ->first()
                            ?->productColorSizes()
                            ->where('size_id', $item->size_id)
                            ->first();

                        if ($variant) {
                            $variant->decrement('quantity', $item->quantity);
                        }

                        $product->inventory()?->decrement('quantity', $item->quantity);

                        Transaction::create([
                            'product_id' => $item->product_id,
                            'type' => \App\Enums\TransactionType::SALE,
                            'quantity' => $item->quantity,
                            'notes' => "Sale of {$item->quantity} units for Order #{$order->id}",
                        ]);
                    }
                }
            }

            $productIds = $order->items->pluck('product_id')->filter()->unique();
            $products = \App\Models\Product::whereIn('id', $productIds)->get();
            StockLevelNotifier::notifyAdminsForLowStock($products);

            $cart->items()->delete();
            $cart->delete();

            $recipientEmail = Auth::check() ? Auth::user()->email : ($contact->email ?? null);
            $language = Auth::check() ? auth()->user()->preferred_language : (request()->getPreferredLanguage(['en', 'ar']) ?? 'en');

            if ($recipientEmail) {
                Mail::to($recipientEmail)->locale($language)->send(new OrderStatusMail($order, $order->status));
            }

            if (!Auth::check() && $contact) {
                $locale = request()->getPreferredLanguage(['en', 'ar']) ?? 'en';

                $invitation = Invitation::create([
                    'email' => $contact->email,
                    'name' => $contact->name ?? null,
                    'phone' => $contact->phone ?? null,
                    'preferred_language' => $locale,
                    'role_id' => Role::where('name', UserRole::Client->value)->first()->id,
                ]);

                Mail::to($contact->email)->locale($locale)->send(new GuestInvitationMail($invitation));
            }
            DB::commit();

            $this->checkoutToken = (string) Str::uuid();
            session()->flash('success', __('Order placed successfully!'));
            return redirect()->route('order.complete')->with('order_success', true);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('order', __('We encountered an issue: :error', ['error' => $e->getMessage()]));
        }
    }

    public function getIsCheckoutReadyProperty()
    {
        return count($this->cartItems) > 0 // Ensure cart is not empty
            && $this->cart->country_id // Ensure country is selected
            && $this->cart->governorate_id; // Ensure governorate is selected
    }

    public function render()
    {
        return view('livewire.checkout', [
            'countries' => Country::all(),
            'governorate' => Governorate::all(),
            'cities' => City::all(),
            'currentRoute' => $this->currentRoute,
            'isCheckoutReady' => $this->isCheckoutReady,
            'paymentMethods' => PaymentMethod::all(),
        ]);
    }
}
