<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Mail\GuestInvitationMail;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Invitation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Notifications\InviteGuestToRegister;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use Spatie\Permission\Models\Role;

class Checkout extends Component
{
    public $currentRoute;
    public $name;
    public $address;
    public $email, $phone, $notes, $create_account = false, $password;
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
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'required|string|max:11',
            'notes' => 'nullable|string',
            'password' => 'nullable|min:6|required_if:create_account,true',
        ];
    }

    public function mount()
    {
        $this->currentRoute = Route::currentRouteName();

        // Load cart data
        $session_id = session()->getId();
        $this->cart = Auth::check()
            ? Cart::where('user_id', Auth::id())->latest()->first()
            : Cart::where('session_id', $session_id)->latest()->first();

        if (Auth::check()) {
            // Load data for authenticated users
            $user = Auth::user();
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone;
            $this->address = $user->address;
        } else {
            // Load data for guest users using session_id
            $session_id = session()->getId();
            $guestContact = Contact::where('session_id', $session_id)->first();

            if ($guestContact) {
                $this->name = $guestContact->name;
                $this->email = $guestContact->email;
                $this->phone = $guestContact->phone;
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
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
            ]);
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
                    'address' => $this->address,
                    'password' => bcrypt($this->password),
                ]);

                Auth::login($user);
                if ($guestContact) {
                    $guestContact->delete();
                }
                return $user;
            } else {
                // Create or update guest contact
                if (!$guestContact) {
                    $guestContact = Contact::create([
                        'session_id' => $session_id,
                        'name' => $this->name,
                        'email' => $this->email,
                        'phone' => $this->phone,
                        'address' => $this->address,
                    ]);
                } else {
                    $guestContact->update([
                        'name' => $this->name,
                        'email' => $this->email,
                        'phone' => $this->phone,
                        'address' => $this->address,
                    ]);
                }
                return $guestContact;
            }
        }
    }

    public function placeOrder()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            // Get the cart for user or guest session
            $cart = Cart::where(function ($query) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                } else {
                    $query->where('session_id', session()->getId());
                }
            })->with('items')->first();

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
            }

            // Save contact details
            $contact = $this->save();

            // Create order
            $order = Order::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'contact_id' => Auth::check() ? null : $contact->id,
                'shipping_type_id' => $cart->shipping_type_id,
                'payment_method_id' => 1,
                'coupon_id' => $cart->coupon_id ?? null,
                'shipping_cost' => $cart->shipping_cost,
                'country_id' => $cart->country_id,
                'governorate_id' => $cart->governorate_id,
                'city_id' => $cart->city_id,
                'tax_percentage' => $cart->tax_percentage,
                'tax_amount' => $cart->tax_amount,
                'subtotal' => $cart->subtotal,
                'total' => $cart->total,
                'status' => 'pending',
                'notes' => $this->notes,
            ]);

            // Transfer cart items to order items
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'bundle_id' => $cartItem->bundle_id,
                    'size_id' => $cartItem->size_id,
                    'color_id' => $cartItem->color_id,
                    'quantity' => $cartItem->quantity,
                    'price_per_unit' => $cartItem->price_per_unit,
                    'subtotal' => $cartItem->subtotal,
                ]);
            }

            // Clear the cart
            $cart->items()->delete();
            $cart->delete();

            // Send an email invite to guest users
            if (!Auth::check() && $contact) {
//                $contact->notify(new InviteGuestToRegister($contact));

                // Assume $this->contact contains the contact email
                $email = $contact->email;

                // Create an invitation record
                $invitation = Invitation::create([
                    'email' => $email,
                    'role_id' => Role::where('name', UserRole::Client->value)->first()->id,
                ]);

                $locale = request()->getPreferredLanguage(['en', 'ar']) ?? 'en';
                // Send invitation email
                Mail::to($email)->send(new GuestInvitationMail($invitation, $locale));
            }

            DB::commit();

            session()->flash('success', __('order_success_message'));

            return redirect()->route('order.complete')->with('order_success', true);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('order', __('order_error_message', ['error' => $e->getMessage()]));
        }
    }


    public function getIsCheckoutReadyProperty()
    {
        $isShippingEnabled = Setting::isShippingLocationsEnabled();

        return count($this->cartItems) > 0 // Ensure cart is not empty
            && (!$isShippingEnabled || ($this->cart->country_id && $this->cart->governorate_id)); // Check location only if shipping is enabled
    }

    public function render()
    {
        return view('livewire.checkout', [
            'countries' => Country::all(),
            'currentRoute' => $this->currentRoute,
            'isCheckoutReady' => $this->isCheckoutReady,
        ]);
    }

    private function prepareJtExpressOrderData($order): array
    {
        $data = [
            'tracking_number'   => '#'. $order->id. ' EGY' . time() . rand(1000, 9999),
            'weight'            => 1.0, // You might want to calculate the total weight dynamically
            'quantity'          => $order->items->sum('quantity'), // Sum of all item quantities in the order

            'remark'            => implode(' , ', array_filter([
                'Notes: ' . ($order->notes ?? 'No notes'),
                $order->user?->name ? 'User: ' . $order->user->name : null,
                $order->user?->email ? 'Email: ' . $order->user->email : null,
                $order->user?->phone ? 'Phone: ' . $order->user->phone : null,
                $order->user?->address ? 'Address: ' . $order->user->address : null,
                $order->contact?->name ? 'Contact: ' . $order->contact->name : null,
                $order->contact?->email ? 'Contact Email: ' . $order->contact->email : null,
                $order->contact?->phone ? 'Contact Phone: ' . $order->contact->phone : null,
                $order->contact?->address ? 'Contact Address: ' . $order->contact->address : null,
            ])),

            'item_name'         => $order->items->pluck('product.name')->implode(', '), // Concatenated product names
            'item_quantity'     => $order->items->count(), // Total distinct items in the order
            'item_value'        => $order->total, // Order total amount
            'item_currency'     => 'EGP',
            'item_description'  => $order->notes ?? 'No description provided',
        ];

        $data['sender'] = [
            'name'                   => 'Your Company Name',
            'company'                => 'Your Company',
            'city'                   => 'Your City',
            'address'                => 'Your Full Address',
            'mobile'                 => 'Your Contact Number',
            'countryCode'            => 'Your Country Code',
            'prov'                   => 'Your Prov',
            'area'                   => 'Your Area',
            'town'                   => 'Your Town',
            'street'                 => 'Your Street',
            'addressBak'             => 'Your Address Bak',
            'postCode'               => 'Your Post Code',
            'phone'                  => 'Your Phone',
            'mailBox'                => 'Your Mail Box',
            'areaCode'               => 'Your Area Code',
            'building'               => 'Your Building',
            'floor'                  => 'Your Floor',
            'flats'                  => 'Your Flats',
            'alternateSenderPhoneNo' => 'Your Alternate Sender Phone No',
        ];

        $data['receiver'] = [
            'name'                      => 'test', // $order->name,
            'prov'                      => 'أسيوط', // $order->region->governorate->name,
            'city'                      => 'القوصية', // $order->region->name,
            'address'                   => 'sdfsacdscdscdsa', // $order->address,
            'mobile'                    => '1441234567', // $order->phone,
            'company'                   => 'guangdongshengshenzhe',
            'countryCode'               => 'EGY',
            'area'                      => 'الصبحه',
            'town'                      => 'town',
            'addressBak'                => 'receivercdsfsafdsaf lkhdlksjlkfjkndskjfnhskjlkafdslkjdshflksjal',
            'street'                    => 'street',
            'postCode'                  => '54830',
            'phone'                     => '23423423423',
            'mailBox'                   => 'ant_li123@qq.com',
            'areaCode'                  => '2342343',
            'building'                  => '13',
            'floor'                     => '25',
            'flats'                     => '47',
            'alternateReceiverPhoneNo'  => $order->another_phone ?? '1231321322',
        ];

        return $data;
    }

    private function updateJtExpressOrder(Order $order, string $shipping_status, $JtExpressOrderData, $jtExpressResponse)
    {
        if (isset($jtExpressResponse['code']) && $jtExpressResponse['code'] == 1) {
            $order->update([
                'tracking_number'   => $JtExpressOrderData['tracking_number'] ?? null,
                'shipping_status'   => $shipping_status,
                'shipping_response' => json_encode($jtExpressResponse)
            ]);
        }
    }
}
