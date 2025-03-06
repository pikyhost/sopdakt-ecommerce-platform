<?php

namespace App\Livewire;

use App\Mail\OrderConfirmationMail;
use App\Models\City;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Services\JtExpressService;
use Barryvdh\Debugbar\DataCollector\LogsCollector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;

class Checkout extends Component
{
    public $currentRoute;
    public $name, $company_name, $country_id;
    public $governorate_id, $city_id, $address, $apartment, $postcode;
    public $email, $phone, $notes, $create_account = false, $password;
    public $governorates = [], $cities = [];
    public $total = 0;
    public $subTotal = 0;
    public $cartItems = [];
    public $shippingCost = 0.0;
    public $taxPercentage;
    public $taxAmount;

   protected function rules()
   {
        return [
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'governorate_id' => 'required|exists:governorates,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'required|string|max:500',
            'apartment' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string',
            'password' => 'nullable|min:6|required_if:create_account,true',
        ];
    }

    public function mount()
    {
        $this->currentRoute = Route::currentRouteName(); // Set route in mount

        if (Auth::check()) {
            // Load data for authenticated users
            $user = Auth::user();
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone;
            $this->address = $user->address;
            $this->company_name = $user->company_name;
            $this->country_id = $user->country_id;
            $this->governorate_id = $user->governorate_id;
            $this->city_id = $user->city_id;
            $this->postcode = $user->postcode;
            $this->apartment = $user->apartment;
        } else {
            // Load data for guest users using session_id
            $session_id = session()->getId();
            $guestContact = Contact::where('session_id', $session_id)->first();

            if ($guestContact) {
                $this->name = $guestContact->name;
                $this->email = $guestContact->email;
                $this->phone = $guestContact->phone;
                $this->address = $guestContact->address;
                $this->company_name = $guestContact->company_name;
                $this->country_id = $guestContact->country_id;
                $this->governorate_id = $guestContact->governorate_id;
                $this->city_id = $guestContact->city_id;
                $this->postcode = $guestContact->postcode;
                $this->apartment = $guestContact->apartment;
            }
        }

        $this->loadCartItems(); // Ensure cart items are loaded
    }

    public function updatedCountryId()
    {
        $this->governorates = Governorate::where('country_id', $this->country_id)->get();
        $this->governorate_id = null;
        $this->cities = [];
        $this->city_id = null;
    }

    public function updatedGovernorateId()
    {
        $this->cities = City::where('governorate_id', $this->governorate_id)->get();
        $this->city_id = null;
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
            return;
        }

        $cartItems = CartItem::where('cart_id', $cart->id)
            ->with('product') // Ensure product is loaded
            ->get();

        $this->cartItems = $cartItems->map(fn($item) => [
            'id' => $item->id,
            'quantity' => $item->quantity,
            'subtotal' => $item->subtotal,
            'product' => $item->product ? [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'price' => $item->product->discount_price_for_current_country ?? 0,
                'feature_product_image_url' => $item->product->getFeatureProductImageUrl() ?? '',
            ] : null,
        ])->toArray();

        $this->subTotal = $cart->subtotal ?? 0;
        $this->total = $cart->total ?? 0;
        $this->shippingCost = $cart->shipping_cost ?? 0;
        $this->country_id = $cart->country_id;
        $this->governorate_id = $cart->governorate_id;

        $this->taxPercentage = Setting::first()?->tax_percentage ?? 0;
        $this->taxAmount = ($this->taxPercentage > 0) ? ($this->subTotal * $this->taxPercentage / 100) : 0;
    }

    public function save()
    {
        $this->validate();

        if (Auth::check()) {
            // Authenticated user - Update their contact info
            $user = Auth::user();
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'country_id' => $this->country_id,
                'governorate_id' => $this->governorate_id,
                'city_id' => $this->city_id,
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
                    'country_id' => $this->country_id,
                    'governorate_id' => $this->governorate_id,
                    'city_id' => $this->city_id,
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
                        'company_name' => $this->company_name,
                        'country_id' => $this->country_id,
                        'governorate_id' => $this->governorate_id,
                        'city_id' => $this->city_id,
                        'postcode' => $this->postcode,
                        'apartment' => $this->apartment,
                    ]);
                } else {
                    $guestContact->update([
                        'name' => $this->name,
                        'email' => $this->email,
                        'phone' => $this->phone,
                        'address' => $this->address,
                        'company_name' => $this->company_name,
                        'country_id' => $this->country_id,
                        'governorate_id' => $this->governorate_id,
                        'city_id' => $this->city_id,
                        'postcode' => $this->postcode,
                        'apartment' => $this->apartment,
                    ]);
                }
                return $guestContact;
            }
        }
    }

    public function placeOrder()
    {
        DB::beginTransaction();
        try {
            // Get the cart for user or guest session
            $cart = Cart::where(function ($query) {
                $query->where('user_id', Auth::id())
                    ->orWhere('session_id', session()->getId());
            })->first();

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
                'country_id' => $cart-> country_id,
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

            $JtExpressOrderData =  $this->prepareJtExpressOrderData($order);
            $jtExpressResponse = app(JtExpressService::class)->createOrder($JtExpressOrderData);
            $this->updateJtExpressOrder($order, 'pending', $JtExpressOrderData,  $jtExpressResponse);

            DB::commit();

            // Send order confirmation email
            Mail::to(Auth::user()->email ?? $contact->email)->queue(new OrderConfirmationMail($order));

            // Set session message for order completion
            session()->flash('success', 'Order placed successfully! A confirmation email has been sent.');


            return redirect()->route('order.complete')->with('order_success', true);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->addError('order', 'Something went wrong: ' . $e->getMessage());

            Log::info('error is:'. $e->getMessage());

            return redirect()->route('cart.index')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function getIsCheckoutReadyProperty()
    {
        return count($this->cartItems) > 0 // Ensure cart is not empty
            && !empty($this->country_id) // Ensure country is selected
            && !empty($this->governorate_id) // Ensure governorate is selected
            && $this->subTotal > 0; // Ensure subtotal is greater than zero
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
            'tracking_number'           => 'EGY' . time() . rand(1000, 9999),
            'weight'                    => 1.0,
            'quantity'                  => 1, // $order->quantity,
            'remark'                    => $order->notes ?? '',
            'item_name'                 => 'Some items',
            'item_quantity'             => 1,
            'item_value'                => $order->total,
            'item_currency'             => 'EGP',
            'item_description'          => $order->notes,
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
                'tracking_number'   => $JtExpressOrderData['tracking_number'],
                'shipping_status'   => $shipping_status,
                'shipping_response' => json_encode($jtExpressResponse)
            ]);
        }
    }
}
