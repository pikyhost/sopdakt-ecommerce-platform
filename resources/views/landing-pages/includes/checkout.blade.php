<div class="modal fade" id="purchaseModal" tabindex="-1" role="dialog" aria-labelledby="purchaseModalLabel" aria-hidden="true" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="purchaseModalLabel">{{ __('Checkout') }}</h5>
                <button type="button" class="close {{ app()->getLocale() === 'ar' ? 'close-left' : 'close-right' }}" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form  method="post" class="px-3" id="purchaseForm" action="{{route('landing-pages.purchase-form.order',$landingPage->id)}}">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <x-input id="name" name="name" required></x-input>
                        </div>

                        <div class="col-md-6 mb-2">
                            <x-input type="text" id="phone" name="phone" pattern="\d*" maxlength="11" required></x-input>
                        </div>

                        <div class="col-md-6 mb-2">
                            <x-input type="text" id="another_phone" name="another_phone" pattern="\d*" maxlength="11"></x-input>
                        </div>

                        <div class="col-md-6 mb-2">
                            <x-select id="governorate" name="governorate_id" label-name="Governorate" onchange="getRegions('governorate', 'region');getShippingCost()" required>
                                @foreach($governorates as $governorate)
                                    <option value="{{$governorate->id}}">{{$governorate->name}}</option>
                                @endforeach
                            </x-select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <x-select id="region" name="region_id" label-name="City" onchange="getShippingCost()"></x-select>
                        </div>

                        @if($landingPage->shippingTypes()->where('landing_page_shipping_types.status',1)->count())
                            <div class="col-md-6 mb-2">
                                <x-select id="shipping_type_id" name="shipping_type_id" label-name="Shipping Type" onchange="getShippingCost()" required>
                                    @foreach($landingPage->shippingTypes as $shippingType)
                                        <option value="{{$shippingType->id}}">{{$shippingType->name}}</option>
                                    @endforeach
                                </x-select>
                            </div>
                        @endif

                        <div class="col-md-6 mb-2">
                            <x-input id="address" name="address" required></x-input>
                        </div>

                        <div class="col-md-6 mb-2">
                            <x-select id="Color" name="color_id" label-name="Color" required>
                                @foreach( $landingPage->colors->unique() as $color)
                                    <option value="{{$color->id}}">{{$color->name}}</option>
                                @endforeach
                            </x-select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <x-select id="Size" name="size_id" label-name="Size" required>
                                @foreach( $landingPage->sizes->unique() as $size)
                                    <option value="{{$size->id}}">{{$size->name}}</option>
                                @endforeach
                            </x-select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <x-input type="number" id="quantity" name="quantity" required min="1"></x-input>
                        </div>

                        <div class="col-md-6 mb-2">
                            <x-text-area id="notes" name="notes" label-name="Important note for the company and the representative"></x-text-area>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 d-flex justify-content-between align-items-center my-2 border-bottom border border-2 py-2">
                            <h6>{{__('Sub total')}}</h6>
                            <h6 id="price">{{$totalPrice}} {{$landingPageSettings?->currency_code}}</h6>
                        </div>

                        <div class="col-12 d-flex justify-content-between align-items-center my-2 border-bottom border border-2 py-2">
                            <h6>{{__('Shipping Cost')}}</h6>
                            <h6 id="shipping_cost">0 {{$landingPageSettings?->currency_code}}</h6>
                        </div>

                        <div class="col-12 d-flex justify-content-between align-items-center my-2 border-bottom border border-2 py-2">
                            <h6>{{__('Total')}}</h6>
                            <h6 id="total">{{$totalPrice}} {{$landingPageSettings?->currency_code}}</h6>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer" style="direction: {{app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                <button type="submit" style="margin-right: 5px;" form="purchaseForm" class="btn btn-primary">{{__('Order')}}</button>
            </div>
        </div>
    </div>
</div>
