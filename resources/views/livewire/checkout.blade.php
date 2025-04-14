<div class="container checkout-container">
    @include('order-wizard')
    <div class="login-form-container">
      @guest
        <h4>Returning customer?
            <button data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" class="btn btn-link btn-toggle">Login</button>
        </h4>
        @endguest

        <div id="collapseOne" class="collapse">
            <div class="login-section feature-box">
                <div class="feature-box-content">
                    <form action="#" id="login-form">
                        <p>
                            If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing & Shipping section.
                        </p>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0 pb-1">Email <span
                                            class="required">*</span></label>
                                    <input type="email" class="form-control" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0 pb-1">Password <span
                                            class="required">*</span></label>
                                    <input type="password" class="form-control" required />
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn">LOGIN</button>

                        <div class="form-footer mb-1">
                            <div class="custom-control custom-checkbox mb-0 mt-0">
                                <input type="checkbox" class="custom-control-input" id="lost-password" />
                                <label class="custom-control-label mb-0" for="lost-password">Remember
                                    me</label>
                            </div>

                            <a href="forgot-password.html" class="forget-password">Lost your password?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="checkout-discount">
        <h4>Have a coupon?
            <button data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseOne" class="btn btn-link btn-toggle">ENTER YOUR CODE</button>
        </h4>

        <div id="collapseTwo" class="collapse">
            <div class="feature-box">
                <div class="feature-box-content">
                    <p>If you have a coupon code, please apply it below.</p>

                    <form action="#">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm w-auto" placeholder="Coupon code" required="" />
                            <div class="input-group-append">
                                <button class="btn btn-sm mt-0" type="submit">
                                    Apply Coupon
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <ul class="checkout-steps">
                <li>
                    <h2 class="step-title">Billing details</h2>

                    @if ($errors->has('auth'))
                        <div class="text-red-500 mb-4">
                            {!! $errors->first('auth') !!}
                        </div>
                    @endif


                    <form wire:submit.prevent="save">
                        <div class="form-group">
                            <label>Name</label><abbr class="required" title="required">*</abbr></label>
                            <input type="text" class="form-control" wire:model.defer="name" />
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Phone <abbr class="required" title="required">*</abbr></label>
                            <input type="tel" class="form-control" wire:model.defer="phone" required />
                            @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Secondary Phone (optional)</label>
                            <input type="tel" class="form-control" wire:model.defer="second_phone"/>
                            @error('second_phone') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Email address <abbr class="required" title="required">*</abbr></label>
                            <input type="email" class="form-control" wire:model.defer="email" required />
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Country / Region <abbr class="required" title="required">*</abbr></label>
                            <input type="text" class="form-control" value="{{ $cart->country->name ?? 'N/A' }}" disabled>
                        </div>

                        <div class="form-group">
                            <label>Governorate <abbr class="required" title="required">*</abbr></label>
                            <input type="text" class="form-control" value="{{ $cart->governorate->name ?? 'N/A' }}" disabled>
                        </div>

                        <div class="form-group">
                            <label>Town / City <abbr class="required" title="required">*</abbr></label>
                            <input type="text" class="form-control" value="{{ $cart->city->name ?? 'N/A' }}" disabled>
                        </div>

                        <div class="form-group">
                            <label>Address details<abbr class="required" title="required">*</abbr></label>
                            <input type="text" placeholder="Street name, Apartment, suite, etc." class="form-control" wire:model.defer="address" required />
                            @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        @guest

                        <div class="form-group mb-1">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="create-account" wire:model.live="create_account" />
                                <label class="custom-control-label" for="create-account">Create an account?</label>
                            </div>
                        </div>

                        @if($create_account)
                            <div class="form-group">
                                <label>Create account password <abbr class="required" title="required">*</abbr></label>
                                <input type="password" placeholder="Password" class="form-control" wire:model.defer="password" required />
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        @endguest

                        <div class="form-group">
                            <label class="order-comments">Order notes (optional)</label>
                            <textarea class="form-control" wire:model.defer="notes" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
                        </div>

                    @if (session()->has('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                </li>
            </ul>
        </div>
        <!-- End .col-lg-8 -->

        <div class="col-lg-5">
            <div class="order-summary">
                <h3>YOUR ORDER</h3>

                <table class="table table-mini-cart">
                    <thead>
                    <tr>
                        <th colspan="2">Products</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($cartItems as $item)
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
                        <td class="price-col">
                            <span>{{ number_format($subTotal, 2) }}</span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <h4>
                              Shipping Cost
                            </h4>
                        </td>
                        <td class="text-end">
                            <span>{{ number_format($shippingCost, 2) }}</span>
                        </td>
                    </tr>
                    <tr class="cart-subtotal">
                        <td><h4>Tax ({{ $taxPercentage }}%)</h4></td>
                        <td class="price-col">
                              <span>
                                  {{ $taxAmount }}
                              </span>
                        </td>
                    </tr>
                    <tr class="order-total">
                        <td><h4>Total</h4></td>
                        <td><b class="total-price">
                                <span>{{ number_format($total, 2) }}</span>
                            </b></td>
                    </tr>
                    </tfoot>
                    @endif
                </table>
                @if ($isCheckoutReady)
                <div class="payment-methods">
                    <h4 class="">Payment methods</h4>
                    <div class="info-box with-icon p-0">
                        <p>
                            Sorry, only Cash On Delivery is the available now.
                        </p>
                    </div>
                </div>
                @endif

                @if ($errors->has('order'))
                    <div class="alert alert-danger">
                        {{ $errors->first('order') }}
                    </div>
                @endif

                @if ($isCheckoutReady)
                    <button wire:click="placeOrder" class="btn btn-dark btn-place-order" wire:loading.attr="disabled">
                        Place order
                        <span wire:loading wire:target="placeOrder">
        <i class="fa fa-spinner fa-spin"></i>
    </span>
                    </button>
                @else
                    <div class="alert alert-warning text-dark" style="display: block; text-align: left;">
                        <strong>Attention!</strong> Some steps are missing.
                        <br>
                        Please complete all required steps before placing your order.
                        <br><br>
                        Click <a href="{{ route('cart.index') }}" class="alert-link"><strong>here</strong></a> to review your cart and complete your purchase steps.
                    </div>

                @endif
            </div>
        <!-- End .col-lg-4 -->
    </div>
    </div>
    <!-- End .row -->
</div>
<!-- End .container -->
