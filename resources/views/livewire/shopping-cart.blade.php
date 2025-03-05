<div class="container">
    @include('order-wizard')
    <div class="row">
        <div class="col-lg-8">
            <div class="cart-table-container">
                <table class="table table-cart">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product / Bundle</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($cartItems as $cartItem)
                        <tr>
                            <td>
                                <figure>
                                    <img src="{{ $cartItem['product']['feature_product_image_url'] ?? $cartItem['bundle']['feature_image_url'] ?? '#' }}"
                                         alt="Product Image"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                </figure>
                            </td>
                            <td>
                                @if ($cartItem['bundle'])
                                    <strong>{{ $cartItem['bundle']['name'] }}</strong>
                                    @if ($cartItem['product'])
                                        <br>
                                        <small>({{ $cartItem['product']['name'] }})</small>
                                    @endif
                                @else
                                    <strong>{{ $cartItem['product']['name'] ?? 'Unknown' }}</strong>
                                @endif
                            </td>
                            <td>{{ $cartItem['size']['name'] ?? 'N/A' }}</td>
                            <td>
                                @if ($cartItem['color'])
                                    <span style="display: inline-block; width: 20px; height: 20px; border-radius: 50%; background-color: {{ $cartItem['color']['code'] }}; border: 1px solid #ccc;"></span>
                                    {{ $cartItem['color']['name'] }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>${{ number_format($cartItem['price_per_unit'] ?? 0, 2) }}</td>
                            <td>
                                @if ($cartItem['bundle'])
                                    <span class="fw-semibold text-primary fs-6">{{ $cartItem['quantity'] }}</span>
                                @else
                                    <div class="d-flex border rounded" style="width: 80px; height: 32px;">
                                        <button wire:click="updateQuantity({{ $cartItem['id'] }}, 'decrease')"
                                                class="btn btn-outline-secondary border-0 px-2 py-0 d-flex align-items-center justify-content-center"
                                                style="width: 30px; font-size: 14px;">
                                            −
                                        </button>

                                        <input type="text" value="{{ $cartItem['quantity'] }}"
                                               class="text-center border-0 fw-bold"
                                               style="width: 20px; font-size: 14px;" readonly>

                                        <button wire:click="updateQuantity({{ $cartItem['id'] }}, 'increase')"
                                                class="btn btn-outline-secondary border-0 px-2 py-0 d-flex align-items-center justify-content-center"
                                                style="width: 30px; font-size: 14px;">
                                            +
                                        </button>
                                    </div>
                                @endif
                            </td>

                            <td class="text-right">
                                $<span wire:key="subtotal-{{ $cartItem['id'] }}">{{ number_format($cartItem['subtotal'], 2) }}</span>
                            </td>
                            <td class="text-center">
                                <button wire:click="removeCartItem({{ $cartItem['id'] }})" style="background: none; border: none; color: red; font-size: 16px; cursor: pointer;">
                                    ❌
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Your cart is empty</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

            </div>
        </div>
        @if (!empty($cartItems))
            <div class="col-lg-4">
            <div class="cart-summary">
                <h3>CART TOTALS</h3>

                <table class="table table-totals">
                    <tbody>
                    <tr>
                        <td>Subtotal</td>
                        <td>${{ number_format((float) $subtotal, 2) }}</td>
                    </tr>
                    </tbody>

                    <tr>
                        <td colspan="2" class="text-left">
                            <h4>Shipping</h4>

                            @foreach ($shipping_types as $shippingMethod)
                                <div class="form-group form-group-custom-control">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input"
                                               wire:model.live="selected_shipping"
                                               value="{{ $shippingMethod->id }}">
                                        <label class="custom-control-label">{{ $shippingMethod->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                            @error('selected_shipping')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror

                            <form action="#">
                                <div class="form-group form-group-sm">
                                    <label>Shipping to <strong>{{ $country_id ? optional(\App\Models\Country::find($country_id))->name : 'Select Country' }}</strong></label>
                                    <div class="select-custom">
                                        <select class="form-control form-control-sm" wire:model.live="country_id">
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('country_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group form-group-sm">
                                    <div class="select-custom">
                                        <select class="form-control form-control-sm" wire:model.live="governorate_id">
                                            <option value="">Select Governorate</option>
                                            @foreach ($governorates as $governorate)
                                                <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('governorate_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group form-group-sm">
                                    <div class="select-custom">
                                        <select class="form-control form-control-sm" wire:model.live="city_id">
                                            <option value="">Select City</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('city_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </form>
                        </td>
                    </tr>
                    <tfoot>

                    <tr class="cart-subtotal">
                        <td><h4>Shipping Cost</h4></td>
                        <td class="price-col">
                            <span>${{ number_format($shippingCost, 2) }}</span>
                        </td>
                    </tr>
                    <tr class="cart-subtotal">
                        <td><h4>Tax ({{ $taxPercentage }}%)</h4></td>
                        <td class="price-col">
                              <span>
                                  {{ $tax }}
                              </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td>${{ number_format((float) $total, 2) }}</td>
                    </tr>
                    </tfoot>
                </table>

                <div class="checkout-methods">
                    <button wire:click="proceedToCheckout" class="btn btn-block btn-dark" wire:loading.attr="disabled">
                        Proceed to Checkout <i class="fa fa-arrow-right"></i>
                        <span wire:loading wire:target="proceedToCheckout">
        <i class="fa fa-spinner fa-spin"></i>
    </span>
                    </button>
                </div>

            </div>
        </div>
        @endif
    </div>
</div>
