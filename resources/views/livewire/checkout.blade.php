<div class="container checkout-container">
    <div class="row">
        <div class="col-lg-7">
            <div class="form" style="flex-direction: column;">
                <!-- Login Section -->
                @guest
                    <div class="returning-customer">
                        Returning customer?
                        <a href="#" data-toggle="collapse" data-target="#loginCollapse" aria-expanded="false" aria-controls="loginCollapse">Login</a>
                    </div>
                    <div id="loginCollapse" class="collapse">
                        <form action="#" wire:submit.prevent="login">
                            <p>If you have shopped with us before, please enter your details below.</p>
                            <label for="loginEmail">Email <span class="required">*</span></label><br>
                            <input type="email" id="loginEmail" wire:model.defer="loginEmail" required /><br>
                            @error('loginEmail') <span class="text-danger">{{ $message }}</span> @enderror

                            <label for="loginPassword">Password <span class="required">*</span></label><br>
                            <input type="password" id="loginPassword" wire:model.defer="loginPassword" required /><br>
                            @error('loginPassword') <span class="text-danger">{{ $message }}</span> @enderror

                            <div class="form-footer">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="remember-me" wire:model="rememberMe" />
                                    <label class="custom-control-label" for="remember-me">Remember me</label>
                                </div>
                                <a href="forgot-password.html" class="forget-password">Lost your password?</a>
                            </div>
                            <button type="submit" class="save-details">Login</button>
                        </form>
                    </div>
                @else
                    <div>
                        <span class="userWelcome">Welcome, {{ auth()->user()->name }}</span>
                        <button class="logoutBtn" wire:click="logout">Logout</button>
                    </div>
                @endguest

                <!-- Coupon Section -->
                <div class="coupon">
                    Have a coupon?
                    <a href="#" data-toggle="collapse" data-target="#couponCollapse" aria-expanded="false" aria-controls="couponCollapse">Enter Your Code</a>
                </div>
                <div id="couponCollapse" class="collapse">
                    <form action="#" wire:submit.prevent="applyCoupon">
                        <p>If you have a coupon code, please apply it below.</p>
                        <input type="text" placeholder="Coupon code" wire:model.defer="couponCode" required />
                        @error('couponCode') <span class="text-danger">{{ $message }}</span> @enderror
                        <button type="submit" class="save-details">Apply Coupon</button>
                    </form>
                </div>

                <!-- Billing Details -->
                <h1 class="title">Billing Details</h1>
                @if ($errors->has('auth'))
                    <div class="text-red-500 mb-4">
                        {!! $errors->first('auth') !!}
                    </div>
                @endif

                @if (session()->has('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form id="shippingForm" wire:submit.prevent="save">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="firstName">First Name <span class="required">*</span></label><br>
                            <input type="text" id="firstName" wire:model.defer="name" required /><br>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="lastName">Last Name <span class="required">*</span></label><br>
                            <input type="text" id="lastName" wire:model.defer="lastName" required /><br>
                            @error('lastName') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <label for="country_id">Country / Region <span class="required">*</span></label><br>
                    <select id="country_id" wire:model="country_id" required>
                        <option value="">Select country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select><br>
                    @error('country_id') <span class="text-danger">{{ $message }}</span> @enderror

                    <label for="governorate_id">Governorate <span class="required">*</span></label><br>
                    <select id="governorate_id" wire:model.live="governorate_id" required>
                        <option value="">Select governorate</option>
                        @foreach($governorates as $governorate)
                            <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                        @endforeach
                    </select><br>
                    @error('governorate_id') <span class="text-danger">{{ $message }}</span> @enderror

                    <label for="city_id">Town / City</label><br>
                    <select id="city_id" wire:model.live="city_id">
                        <option value="">Select city</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select><br>
                    @error('city_id') <span class="text-danger">{{ $message }}</span> @enderror

                    <label for="address">Address Details <span class="required">*</span></label><br>
                    <input type="text" id="address" wire:model.defer="address" placeholder="Street name, Apartment, suite, etc." required /><br>
                    @error('address') <span class="text-danger">{{ $message }}</span> @enderror

                    <label for="phone">Phone <span class="required">*</span></label><br>
                    <input type="tel" id="phone" wire:model.defer="phone" placeholder="Example: 01012345678" required /><br>
                    @error('phone') <span class="text-danger">{{ $message }}</span> @enderror

                    <label for="second_phone">Secondary Phone (optional)</label><br>
                    <input type="tel" id="second_phone" wire:model.defer="second_phone" /><br>
                    @error('second_phone') <span class="text-danger">{{ $message }}</span> @enderror

                    <label for="email">Email Address <span class="required">*</span></label><br>
                    <input type="email" id="email" wire:model.defer="email" required /><br>
                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror

                    @if(\App\Models\Setting::isShippingEnabled())
                        <label for="selected_shipping">Shipping Type <span class="required">*</span></label><br>
                        <select id="selected_shipping" wire:model="selected_shipping" required>
                            <option value="">Select shipping type</option>
                            @foreach($shipping_types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }} ({{ number_format($type->cost, 2) }} {{ $cartItems[0]['currency'] ?? 'USD' }})</option>
                            @endforeach
                        </select><br>
                        @error('selected_shipping') <span class="text-danger">{{ $message }}</span> @enderror
                    @endif

                    @guest
                        <div class="create-account">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="create-account" wire:model="create_account">
                                <label class="custom-control-label" for="create-account">Create an account?</label>
                            </div>
                        </div>
                        @if($create_account)
                            <div id="create_account">
                                <label for="password">Create Account Password <span class="required">*</span></label><br>
                                <input type="password" id="password" wire:model.defer="password" required /><br>
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    @endguest

                    <div class="order-notes">
                        Order Notes (optional) <br>
                        <textarea class="orderNotes" id="orderNotes" wire:model.defer="notes" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
                    </div>

                    <button class="save-details" type="submit">Save Details</button>
                </form>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-5">
            <div class="your-order">
                <h3>YOUR ORDER</h3><br>
                <table id="checkoutCartTable" class="table table-mini-cart">
                    <thead>
                    <tr>
                        <th colspan="2">Products</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($this->cartItems as $item)
                        <tr>
                            <td class="product-col">
                                <h3 class="product-title">
                                    <span class="product-qty">{{ $item['quantity'] }}x</span>
                                    {{ $item['product']['name'] ?? 'Product not found' }}
                                </h3>
                            </td>
                            <td class="price-col">
                                <span>{{ $item['subtotal'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">Your cart is empty</td>
                        </tr>
                    @endforelse
                    </tbody>
                    @if ($isCheckoutReady)
                        <tfoot>
                        <tr class="cart-subtotal">
                            <td><h4>Subtotal</h4></td>
                            <td class="price-col"><span>{{ number_format($subTotal, 2) }} {{ $cartItems[0]['currency'] ?? 'USD' }}</span></td>
                        </tr>
                        <tr>
                            <td><h4>Shipping Cost</h4></td>
                            <td class="text-end"><span>{{ number_format($shippingCost, 2) }} {{ $cartItems[0]['currency'] ?? 'USD' }}</span></td>
                        </tr>
                        <tr class="cart-subtotal">
                            <td><h4>Tax ({{ $taxPercentage }}%)</h4></td>
                            <td class="price-col"><span>{{ number_format($taxAmount, 2) }} {{ $cartItems[0]['currency'] ?? 'USD' }}</span></td>
                        </tr>
                        <tr class="order-total">
                            <td><h4>Total</h4></td>
                            <td><b class="total-price"><span>{{ number_format($total, 2) }} {{ $cartItems[0]['currency'] ?? 'USD' }}</span></b></td>
                        </tr>
                        </tfoot>
                    @endif
                </table>

                <!-- Fallback Table -->
                <table id="fallbackCheckout" style="display: none;">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Sample Product</td>
                        <td>150.00 USD</td>
                        <td>X 1</td>
                        <td>150.00 USD</td>
                    </tr>
                    </tbody>
                </table>

                <!-- Payment Methods -->
                @if ($isCheckoutReady)
                    <div class="payment-methods">
                        <div class="title">Payment Methods</div><br>
                        @if (empty($paymentMethods))
                            <i style="color:rgba(91, 192, 222, 1);" class="fa fa-solid fa-circle-info"></i>
                            <span class="info">
                                Sorry, it appears that there are no available payment methods for your state.
                                Please contact us if you require assistance or wish to make alternate arrangements.
                            </span><br>
                        @else
                            <div class="payment-options">
                                @foreach($paymentMethods as $method)
                                    <div class="payment-option mb-2">
                                        <input type="radio"
                                               id="payment-method-{{ $method->id }}"
                                               name="payment_method"
                                               wire:model="payment_method_id"
                                               value="{{ $method->id }}"
                                               class="custom-control-input">
                                        <label for="payment-method-{{ $method->id }}" class="payment-label">
                                            <span class="payment-name h6">{{ $method->name }}</span>
                                            <span class="payment-desc">{{ $method->description }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('payment_method_id') <span class="text-danger">{{ $message }}</span> @enderror
                        @endif

                        @if($errors->has('payment'))
                            <div class="alert alert-danger mt-2">
                                {{ $errors->first('payment') }}
                            </div>
                        @endif
                    </div>

                    @if ($paymentUrl)
                        <div class="mt-4">
                            <iframe src="{!! $paymentUrl !!}" width="100%" height="600" frameborder="0"></iframe>
                        </div>
                    @endif

                    <button wire:click="placeOrder" class="btn btn-dark btn-place-order" wire:loading.attr="disabled">
                        Place Order
                        <span wire:loading wire:target="placeOrder">
                            <i class="fa fa-spinner fa-spin"></i>
                        </span>
                    </button>
                @else
                    <div class="alert alert-warning text-dark" style="display: block; text-align: left;">
                        <strong>Attention!</strong> Some steps are missing.<br>
                        Please complete all required steps before placing your order.<br><br>
                        Click <a href="{{ url('/cart') }}" class="alert-link"><strong>here</strong></a> to review your cart and complete your purchase steps.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Styles -->
    <style>
        .container.checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .returning-customer, .coupon {
            font-size: 1rem;
            margin: 10px 0;
        }
        .returning-customer a, .coupon a {
            color: #007bff;
            text-decoration: none;
        }
        .title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .form input, .form select, .form textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form label {
            font-weight: 600;
        }
        .required {
            color: #e02b27;
            font-size: 1.2em;
        }
        .save-details, .btn.btn-dark.btn-place-order {
            background: #333;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .your-order {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        .table-mini-cart {
            width: 100%;
            border-collapse: collapse;
        }
        .table-mini-cart th, .table-mini-cart td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .payment-methods {
            margin-top: 20px;
        }
        .payment-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        .payment-label {
            display: block;
            padding: 1rem 1rem 1rem 2.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-option input[type="radio"]:checked + .payment-label {
            border-color: #333;
            background-color: #f9f9f9;
        }
        .payment-name {
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }
        .payment-desc {
            display: block;
            font-size: 0.9rem;
            color: #777;
            margin-top: 0.25rem;
        }
        .text-danger {
            color: #e02b27;
            font-size: 0.9rem;
        }
        .alert {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeeba;
        }
        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .payment-method-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .step-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
        }
    </style>

    <!-- JavaScript -->
    <script>
        window.addEventListener('payment-url-updated', () => {
            console.log('Payment URL updated, iframe should render');
        });
    </script>
</div>
