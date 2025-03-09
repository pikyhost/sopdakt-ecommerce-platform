<div>
    <div class="product-header">
        @if ($product->bundles->isNotEmpty())
            <div x-data="{ showBundles: false }" class="product-bundles">
                <div class="bundle-header">
                    <h3 class="bundle-title">{{ __('Available Bundles') }}</h3>
                    <button @click="showBundles = !showBundles" class="toggle-button">
                        <span x-text="showBundles ? '{{ __('Hide Bundles') }}' : '{{ __('Show Bundles') }}'"></span>
                        <svg x-show="!showBundles" class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                        <svg x-show="showBundles" class="icon rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>

                <ul class="bundle-list" x-show="showBundles" x-collapse>
                    @foreach ($product->bundles as $bundle)
                        <li class="bundle-item">
                            <div class="bundle-content">
                                <strong class="bundle-name">{{ $bundle->getTranslation('name', app()->getLocale()) }}</strong>
                               <br>
                                <p class="bundle-name">{{ $bundle->discount_price }}</p>
                                <div class="bundle-details">
                                    @switch($bundle->bundle_type)
                                        @case(\App\Enums\BundleType::BUY_X_GET_Y)
                                            @if (!is_null($bundle->buy_x) && !is_null($bundle->get_y))
                                                <p class="bundle-text">
                                                    {{ __('Buy :x and get :y free', ['x' => $bundle->buy_x, 'y' => $bundle->get_y]) }}
                                                    @if (!is_null($bundle->bundle_discount_price_for_current_country))
                                                        {{ __('with a discount price of :price', [
                                                            'price' => number_format($bundle->bundle_discount_price_for_current_country, 2)
                                                        ]) }}
                                                    @endif
                                                </p>
                                            @elseif (!is_null($bundle->buy_x) && is_null($bundle->get_y) && isset($bundle->bundle_discount_price_for_current_country))
                                                <p class="bundle-text">
                                                    {{ __('Buy :x with a discount price of :price', [
                                                        'x' => $bundle->buy_x,
                                                        'price' => number_format($bundle->bundle_discount_price_for_current_country, 2)
                                                    ]) }}
                                                </p>
                                            @endif
                                            @break

                                        @case(\App\Enums\BundleType::FIXED_PRICE)
                                            @php
                                                $discountPrice = $bundle->bundle_discount_price_for_current_country;
                                                $originalPrice = $bundle->bundle_price_for_current_country;
                                            @endphp

                                            <p class="bundle-text">
                                                @if (isset($discountPrice, $originalPrice) && $discountPrice < $originalPrice)
                                                    {{ __('Get this bundle for :price instead of :original', [
                                                        'price' => number_format($discountPrice, 2),
                                                        'original' => number_format($originalPrice, 2)
                                                    ]) }}
                                                @else
                                                    {{ __('Get this bundle for :price', ['price' => number_format($originalPrice, 2)]) }}
                                                @endif
                                            </p>
                                            @break
                                    @endswitch
                                </div>

                                <!-- Bundle Products -->
                                <div class="bundle-products">
                                    @foreach ($bundle->products as $bundleProduct)
                                        <div class="product-item">
                                            <a href="{{ route('product.show', $bundleProduct->slug) }}">
                                                <img src="{{ $bundleProduct->getFeatureProductImageUrl() }}" class="product-image" alt="{{ $bundleProduct->name }}">
                                            </a>
                                            <div class="product-info">
                                                <span class="product-name">{{ $bundleProduct->name }}</span>
                                                @if ($bundle->bundle_type !== \App\Enums\BundleType::BUY_X_GET_Y)
                                                    <span class="product-quantity">
                                                    ({{ __('Quantity:') }} 1)
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <br>
                                <!-- Cart Error Message -->
                                @if ($errors->has('cart_error'))
                                    <div class="alert alert-danger mt-3">
                                        {{ $errors->first('cart_error') }}
                                    </div>
                                @endif
                                <button wire:click="selectBundle({{ $bundle->id }})"
                                        class="btn btn-success"
                                        wire:loading.attr="disabled"
                                        wire:target="selectBundle">
                                    <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                    <span wire:loading wire:target="selectBundle">
        <i class="fa fa-spinner fa-spin ms-1"></i>
    </span>
                                </button>

                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Bundle Selection Modal -->
    @if ($showModal)
        <div class="modal show" style="display:block;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Select Options for Bundle') }}</h5>
                        <button type="button" class="close" wire:click="$set('showModal', false)">&times;</button>
                    </div>
                    <div class="modal-body">
                        @foreach ($selectedBundle->products as $bundleProduct)
                            @php
                                $totalInputs = ($selectedBundle->bundle_type === \App\Enums\BundleType::BUY_X_GET_Y)
                                    ? ($selectedBundle->buy_x + $selectedBundle->get_y)
                                    : 1;
                            @endphp

                            @for ($i = 0; $i < $totalInputs; $i++)
                                <div class="mb-3">
                                    <h6>{{ $bundleProduct->name }} ({{ __('Item') }} {{ $i + 1 }})</h6>

                                    {{-- Only show color & size inputs if the product has color options --}}
                                    @if (isset($colors[$bundleProduct->id]) && $colors[$bundleProduct->id]->isNotEmpty())
                                        <label>{{ __('Color') }}</label>
                                        <select wire:model.live="selections.{{ $bundleProduct->id }}.{{ $i }}.color_id" class="form-control">
                                            <option value="">{{ __('Select Color') }}</option>
                                            @foreach ($colors[$bundleProduct->id] ?? [] as $color)
                                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                                            @endforeach
                                        </select>
                                        @error("selections.{$bundleProduct->id}.{$i}.color_id")
                                        <span class="text-danger">{{ $message }}</span>
                                        <br>
                                        @enderror

                                        <label>{{ __('Size') }}</label>
                                        <select wire:model.live="selections.{{ $bundleProduct->id }}.{{ $i }}.size_id" class="form-control">
                                            <option value="">{{ __('Select Size') }}</option>
                                            @foreach ($sizes[$bundleProduct->id][$i] ?? [] as $size)
                                                <option value="{{ $size->id }}">{{ $size->name }}</option>
                                            @endforeach
                                        </select>
                                        @error("selections.{$bundleProduct->id}.{$i}.size_id")
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    @endif
                                </div>
                            @endfor
                        @endforeach
                    </div>

                    <div class="modal-footer">
                        <button wire:click="addToCart"
                                wire:loading.attr="disabled"
                                wire:target="addToCart"
                                class="btn btn-success">
                            <i class="fas fa-cart-plus me-1"></i>
                            {{ __('Add to Cart') }}

                            {{-- Loader Icon (Only Shows When addToCart is Processing) --}}
                            <span wire:loading wire:target="addToCart">
        <i class="fas fa-spinner fa-spin ms-2"></i>
    </span>
                        </button>

                        <button class="btn btn-secondary" wire:click="$set('showModal', false)">{{ __('Cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
