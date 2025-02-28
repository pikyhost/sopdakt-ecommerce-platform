<!DOCTYPE html>
<html lang="en">
@include('landing-pages.includes.head-section', ['showSettings' => false])
<body>
    <form action="{{route('landing-pages.purchase-form.store', $landingPage->id)}}" id="checkout-form" method="post">
        @csrf

        <x-widget-container style="background-color: #FFF">
            <div class="page-wrapper p-0" style="direction: {{app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}; background-color: #FFF">
                <main class="main main-test ">
                    <div class="container-fluid  ">
                        <div class="row">
                            <div class="col-lg-7">
                                <ul class="checkout-steps ">
                                    <li>
                                        <h4 class="step-title"style="text-align: {{app()->getLocale() == 'ar' ? 'right' : 'left'}}">{{__('Billing details')}}</h4>
                                        <div class=" row">
                                            <div class="col-md-6 mb-2">
                                                <x-input class="rounded-0 form-control" id="name" name="name" required></x-input>
                                            </div>

                                            <div class="col-md-6 mb-2">
                                                <x-input type="number" class="rounded-0 form-control" id="phone" name="phone" pattern="\d*" maxlength="11" required></x-input>
                                            </div>

                                            <div class="col-md-6 mb-2">
                                                <x-input type="number" class="rounded-0 form-control" id="another_phone" name="another_phone" pattern="\d*" maxlength="11"></x-input>
                                            </div>

                                            <div class="col-md-6 mb-2">
                                                <x-select class="rounded-0 form-control" id="governorate" name="governorate_id" label-name="Governorate" onchange="getRegions('governorate', 'region');getShippingCost()" required>
                                                    @foreach($governorates as $governorate)
                                                        <option @if(old('governorate_id') == $governorate->id) selected @endif value="{{$governorate->id}}">{{$governorate->name}}</option>
                                                    @endforeach
                                                </x-select>
                                            </div>

                                            <div class="col-md-6 mb-2">
                                                <x-select class="rounded-0 form-control" id="region" name="region_id" label-name="City" onchange="getShippingCost()"></x-select>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <x-input class="rounded-0 form-control" id="address" name="address" label-name="Address" required></x-input>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <x-text-area id="notes" name="notes"></x-text-area>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="col-lg-5 border py-3 h-auto" style="text-align: {{app()->getLocale() == 'ar' ? 'right' : 'left'}}">
                                <div class="order-summary">
                                    <h4>{{__('Order Summary')}}</h4>

                                    <table class="table table-mini-cart">
                                        <tfoot>
                                            <tr class="cart-subtotal">
                                                <td><h4>{{__('Bundle')}}</h4></td>
                                                <td class="price-col"><span>{{$bundle->name}}</span></td>
                                            </tr>

                                            <tr class="cart-subtotal">
                                                <td><h4>{{__('Quantity')}}</h4></td>

                                                <td class="price-col"><span>{{$quantity}} {{$quantity>1? __('Bundles'): __('Bundle')}}</span></td>
                                            </tr>

                                            <tr>
                                                <td colspan="2"><h4>{{__('Items')}}</h4></td>
                                            </tr>

                                            <tr class="cart-subtotal">
                                                <th>{{__('Color')}}</th>
                                                <th>{{__('Size')}}</th>
                                            </tr>

                                            <input type="hidden" name="bundle_landing_page_id" value="{{$bundle->id}}">
                                            <input type="hidden" name="quantity" value="{{$quantity}}">

                                            @foreach($varieties as $index=>$variety)
                                                <input type="hidden" name="varieties[{{$index}}][color_id]" value="{{$variety['color_id']}}">
                                                <input type="hidden" name="varieties[{{$index}}][size_id]" value="{{$variety['size_id']}}">

                                                <tr class="cart-subtotal">
                                                    <td>
                                                        <span>{{\App\Models\Color::find($variety['color_id'])->name}}</span>
                                                    </td>

                                                    <td>
                                                        <span>{{\App\Models\Size::find($variety['size_id'])->name}}</span>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            <tr class="cart-subtotal">
                                                <td><h4>{{__('Shipping Cost')}}</h4></td>
                                                <td class="price-col text-end"><span id="shipping_cost"></span></td>
                                            </tr>

                                            <tr class="cart-subtotal">
                                                <td><h4>{{__('Subtotal')}}</h4></td>

                                                <td class="price-col">
                                                    <span id="price">{{$totalPrice .' '. $settings?->currency_code}}</span>
                                                </td>
                                            </tr>

                                            @if($landingPage->shippingTypes()->where('landing_page_shipping_types.status',1)->count() > 0)
                                                <tr class="order-shipping">
                                                    <td colspan="2">
                                                        <h4 class="m-b-sm">{{__('Shipping Type')}}</h4>

                                                        @foreach($landingPage->shippingTypes as $shippingType)
                                                            <div class="form-group form-group-custom-control">
                                                                <div class="custom-control custom-radio d-flex">
                                                                    <input value="{{$shippingType->id}}" id="shipping-{{$shippingType->id}}" type="radio" class="custom-control-input" name="shipping_type_id" required/>
                                                                    <label for="shipping-{{$shippingType->id}}" class="custom-control-label">{{$shippingType->name}}</label>
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                        @error('shipping_type_id')
                                                            <span class="text-danger">{{$message}}</span>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            @endif

                                            <tr class="order-total">
                                                <td><h4>{{__('Total')}}</h4></td>
                                                <td><b class="total-price"><span id="total">{{$totalPrice .' '. $settings?->currency_code}}</span></b></td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <button type="submit" class="btn btn-dark w-100 rounded-0" form="checkout-form">
                                        {{__('Place order')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </x-widget-container>
    </form>

    @include('landing-pages.includes.script-section')
</body>
</html>
