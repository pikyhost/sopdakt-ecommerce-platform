<div>
    <div class="container py-5">
        @if ($product->bundles->isNotEmpty())
            <!-- Title Section -->
            <div class="text-center mb-5">
                <h1 class="fw-bold text-dark display-5">
                    <i class="fas fa-gift text-warning"></i> {{ __('Special Bundle Offers!') }}
                </h1>
                <p class="text-muted fs-5">{{ __('Get the best value deals on our premium bundles.') }}</p>
            </div>

            <div class="row g-4">
                @foreach ($product->bundles as $bundle)
                    <div class="col-md-6 col-lg-6">
                        <div class="card shadow-lg border-0 rounded-4 h-100">
                            <!-- Header -->
                            <div class="card-header bg-primary text-white text-center py-3 rounded-top">
                                <h3 class="fw-bold mb-0">{{ $bundle->getTranslation('name', app()->getLocale()) }}</h3>
                            </div>

                            <!-- Body -->
                            <div class="card-body p-4">
                                <div class="text-center">
                                <span class="badge bg-success fs-4 px-4 py-2">
                                    <i class="fas fa-tag"></i> ${{ number_format($bundle->discount_price, 2) }}
                                </span>
                                </div>

                                <div class="mt-4 text-center">
                                    @switch($bundle->bundle_type)
                                        @case(\App\Enums\BundleType::BUY_X_GET_Y)
                                            @if (!is_null($bundle->buy_x) && !is_null($bundle->get_y))
                                                <p class="fs-5 text-muted">
                                                    <i class="fas fa-cart-plus text-warning fs-4"></i>
                                                    {{ __('Buy :x and get :y free', ['x' => $bundle->buy_x, 'y' => $bundle->get_y]) }}
                                                    @if (!is_null($bundle->bundle_discount_price_for_current_country))
                                                        <br>
                                                        <span class="badge bg-warning text-dark fs-5">
                                                        {{ __('Discount Price: $:price', ['price' => number_format($bundle->bundle_discount_price_for_current_country, 2)]) }}
                                                    </span>
                                                    @endif
                                                </p>
                                            @endif
                                            @break
                                        @case(\App\Enums\BundleType::FIXED_PRICE)
                                            @php
                                                $discountPrice = $bundle->bundle_discount_price_for_current_country;
                                                $originalPrice = $bundle->bundle_price_for_current_country;
                                            @endphp
                                            <p class="fs-5 text-muted">
                                                <i class="fas fa-tag text-danger fs-4"></i>
                                                @if ($discountPrice < $originalPrice)
                                                    {{ __('Get this bundle for :price instead of :original', [
                                                        'price' => number_format($discountPrice, 2),
                                                        'original' => number_format($originalPrice, 2)
                                                    ]) }}
                                                @else
                                                    {{ __('Get this bundle for :price', ['price' => number_format($originalPrice, 2)]) }}
                                                @endif
                                                <br>
                                                <span class="badge bg-warning text-dark fs-5">
                                                        {{ __('Discount Price: $:price', ['price' => number_format($bundle->bundle_discount_price_for_current_country, 2)]) }}
                                                    </span>
                                            </p>
                                            @break
                                    @endswitch
                                </div>

                                <!-- Bundle Products -->
                                <div class="bundle-products mt-4">
                                    <div class="d-flex flex-wrap justify-content-center gap-3">
                                        @foreach ($bundle->products as $bundleProduct)
                                            <div class="card shadow-sm border-0 rounded-3 text-center" style="width: 130px;">
                                                <a href="{{ route('product.show', $bundleProduct->slug) }}" class="text-decoration-none">
                                                    <img src="{{ $bundleProduct->getFeatureProductImageUrl() }}"
                                                         class="card-img-top rounded-top product-image"
                                                         style="height: 100px; object-fit: cover;">
                                                </a>
                                                <div class="card-body p-2">
                                                    <span class="fw-bold fs-6 d-block">{{ $bundleProduct->name }}</span>
                                                    @if ($bundle->bundle_type !== \App\Enums\BundleType::BUY_X_GET_Y)
                                                        <span class="badge bg-secondary px-3 py-2 fs-6">{{ __('Quantity: 1') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Error Message -->
                            @if ($errors->has('cart_error'))
                                <div class="alert alert-danger text-center fs-5 m-3">
                                    {{ $errors->first('cart_error') }}
                                </div>
                            @endif

                            <!-- Footer -->
                            <div class="card-footer text-center bg-light py-4 rounded-bottom">
                                <button wire:click="selectBundle({{ $bundle->id }})"
                                        class="btn btn-lg btn-primary w-100 fw-bold d-flex align-items-center justify-content-center py-3"
                                        wire:loading.attr="disabled"
                                        wire:target="selectBundle">
                                    <i class="fas fa-cart-plus me-2"></i> {{ __('Add to Cart') }}
                                    <span wire:loading wire:target="selectBundle">
                                    <i class="fa fa-spinner fa-spin ms-2"></i>
                                </span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

@if ($showModal)
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">

                    <!-- 🟢 Header -->
                    <div class="modal-header bg-gradient bg-primary text-white py-4">
                        <h3 class="modal-title fw-bold">
                            <i class="fas fa-box-open me-2"></i> {{ __('Select Options for Bundle') }}
                        </h3>
                        <button type="button" class="btn-close btn-close-white fs-3" wire:click="$set('showModal', false)"></button>
                    </div>

                    <!-- 🔵 Body -->
                    <div class="modal-body p-5">
                        <div class="row row-cols-1 row-cols-md-2 g-4">
                            @foreach ($selectedBundle->products as $bundleProduct)
                                @php
                                    $totalInputs = ($selectedBundle->bundle_type === \App\Enums\BundleType::BUY_X_GET_Y)
                                        ? ($selectedBundle->buy_x + $selectedBundle->get_y)
                                        : 1;
                                @endphp

                                @for ($i = 0; $i < $totalInputs; $i++)
                                    <div class="col">
                                        <div class="card border-0 shadow-sm p-4 rounded-3">
                                            <h5 class="fw-bold text-primary">
                                                <i class="fas fa-tag me-1"></i> {{ $bundleProduct->name }}
                                                <small class="text-muted">({{ __('Item') }} {{ $i + 1 }})</small>
                                            </h5>

                                            @if (isset($colors[$bundleProduct->id]) && $colors[$bundleProduct->id]->isNotEmpty())
                                                <div class="form-floating mb-4">
                                                    <select wire:model.live="selections.{{ $bundleProduct->id }}.{{ $i }}.color_id"
                                                            class="form-select form-control-lg border-primary shadow-sm">
                                                        <option value="">{{ __('Select Color') }}</option>
                                                        @foreach ($colors[$bundleProduct->id] ?? [] as $color)
                                                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label class="fs-5">{{ __('Color') }}</label>
                                                    @error("selections.{$bundleProduct->id}.{$i}.color_id")
                                                    <small class="text-danger fs-6">{{ $message }}</small>
                                                    @enderror
                                                </div>

                                                <div class="form-floating mb-4">
                                                    <select wire:model.live="selections.{{ $bundleProduct->id }}.{{ $i }}.size_id"
                                                            class="form-select form-control-lg border-primary shadow-sm">
                                                        <option value="">{{ __('Select Size') }}</option>
                                                        @foreach ($sizes[$bundleProduct->id][$i] ?? [] as $size)
                                                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label class="fs-5">{{ __('Size') }}</label>
                                                    @error("selections.{$bundleProduct->id}.{$i}.size_id")
                                                    <small class="text-danger fs-6">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endfor
                            @endforeach
                        </div>
                    </div>

                    <!-- 🟡 Footer -->
                    <div class="modal-footer d-flex justify-content-between px-5 py-4">
                        <button wire:click="addToCart"
                                wire:loading.attr="disabled"
                                wire:target="addToCart"
                                class="btn btn-success btn-lg rounded-pill d-flex align-items-center px-5 py-3 fs-5 fw-bold">
                            <i class="fas fa-cart-plus me-2"></i> {{ __('Add to Cart') }}
                            <span wire:loading wire:target="addToCart" class="ms-2">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                        </button>

                        <button class="btn btn-outline-secondary btn-lg rounded-pill px-5 py-3 fs-5 fw-bold"
                                wire:click="$set('showModal', false)">
                            <i class="fas fa-times me-2"></i> {{ __('Cancel') }}
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>
