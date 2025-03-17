<div>
    <div class="container py-5" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        @if ($product->bundles->isNotEmpty())
            <div class="text-center mb-5">
                <h1 class="fw-bold text-dark display-5">
                    <i class="fas fa-gift text-warning me-2"></i> {{ __('Special Bundle Offers!') }}
                </h1>
                <p class="text-muted fs-5">{{ __('Get the best value deals on our premium bundles.') }}</p>
            </div>

            <div class="row g-4">
                @foreach ($product->bundles as $bundle)
                    <div class="col-md-6 col-lg-6">
                        <div class="card shadow-lg border-0 rounded-4 h-100 d-flex flex-column overflow-hidden hover-scale">
                            <div class="card-header bg-gradient-primary text-white text-center py-4 rounded-top">
                                <h3 class="fw-bold mb-0">{{ $bundle->getTranslation('name', app()->getLocale()) }}</h3>
                            </div>

                            <div class="card-body p-4 d-flex flex-column flex-grow-1">
                                <div class="text-center mb-4">
                                    <span class="badge bg-success fs-3 px-4 py-2 shadow-sm">
                                        <i class="fas fa-tag me-2"></i> {{ $bundle->bundle_discount_price_for_current_country }}
                                    </span>
                                </div>

                                <div class="text-center mb-4">
                                    @switch($bundle->bundle_type)
                                        @case(\App\Enums\BundleType::BUY_X_GET_Y)
                                            @if (!is_null($bundle->buy_x) && !is_null($bundle->get_y))
                                                <p class="fs-5 text-muted">
                                                    <i class="fas fa-cart-plus text-warning me-2 fs-4"></i>
                                                    {{ __('Buy :x and get :y free', ['x' => $bundle->buy_x, 'y' => $bundle->get_y]) }}
                                                    @if (!is_null($bundle->bundle_discount_price_for_current_country))
                                                        <br>
                                                        <span class="badge bg-warning text-dark fs-5 mt-2">
                                                            {{ __('Discount Price: :price', ['price' => $bundle->bundle_discount_price_for_current_country]) }}
                                                        </span>
                                                    @endif
                                                </p>
                                            @endif
                                            @break
                                        @case(\App\Enums\BundleType::FIXED_PRICE)
                                            @php
                                                $discountPrice = $bundle->bundle_discount_price_for_current_country;
                                            @endphp
                                            <p class="fs-5 text-muted">
                                                <i class="fas fa-tag text-danger me-2 fs-4"></i>
                                                {{ __('Get this bundle now') }}
                                                <br>
                                                <span class="badge bg-warning text-dark fs-5 mt-2">
                                                    {{ __('Discount Price: :price', ['price' => $discountPrice]) }}
                                                </span>
                                            </p>
                                            @break
                                    @endswitch
                                </div>

                                <div class="bundle-products mt-4 flex-grow-1">
                                    <div class="d-flex flex-wrap justify-content-center gap-3">
                                        @foreach ($bundle->products as $bundleProduct)
                                            @php
                                                $imageUrl = $bundleProduct->getFeatureProductImageUrl();
                                                $productUrl = route('product.show', $bundleProduct->slug);
                                                $productName = $bundleProduct->name;
                                                $discountPrice = $bundleProduct->discount_price_for_current_country;
                                            @endphp

                                            <div class="card shadow-sm border-0 rounded-3 text-center p-2 d-flex flex-column align-items-center product-card">
                                                <a href="{{ $productUrl }}" class="text-decoration-none">
                                                    <img src="{{ $imageUrl }}"
                                                         class="card-img-top rounded-top"
                                                         style="height: 120px; object-fit: contain; width: 100%;">
                                                </a>
                                                <div class="card-body p-2">
                                                    <span class="fw-bold fs-6 d-block text-truncate">{{ $productName }}</span>

                                                    @if ($bundle->bundle_type !== \App\Enums\BundleType::BUY_X_GET_Y)
                                                        <span class="badge bg-secondary px-3 py-2 fs-6 d-block mt-2">
                                                            {{ __('Quantity: 1') }}
                                                        </span>
                                                    @endif

                                                    <span class="text-muted fs-6 d-block mt-2 mb-0">
                                                        <s class="text-danger">{{ $discountPrice }}</s>
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            @if ($errors->has('cart_error'))
                                <div class="alert alert-danger text-center fs-5 m-3">
                                    {{ $errors->first('cart_error') }}
                                </div>
                            @endif
                            <div class="card-footer text-center bg-light py-3 rounded-bottom">
                                <button wire:click="selectBundle({{ $bundle->id }})"
                                        class="btn btn-primary btn-lg w-100 px-4 py-3 shadow-sm rounded-pill fw-bold d-flex align-items-center justify-content-center"
                                        wire:loading.attr="disabled"
                                        wire:target="selectBundle"
                                        style="gap: 10px;">
                                    <i class="fa fa-shopping-cart"></i>
                                    {{ __('Add to Cart') }}
                                    <span wire:loading wire:target="selectBundle">
            <i class="fa fa-spinner fa-spin"></i>
        </span>
                                </button>
                            </div>



                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

<style>
        /* Hover Scale Effect */
        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        /* Product Card Hover Effect */
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Button Hover Effect */
        .hover-effect {
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .hover-effect:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        /* Gradient Background for Card Header */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        /* Larger Price Text */
        .fs-3 {
            font-size: 1.75rem !important;
        }

        /* Better Spacing */
        .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        .mt-4 {
            margin-top: 1.5rem !important;
        }

        .py-3 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }
    </style>
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

                    <!-- 🟢 Header -->
                    <div class="modal-header bg-gradient bg-primary text-white py-4">
                        <h3 class="modal-title fw-bold">
                            <i class="fas fa-box-open me-2"></i> {{ __('bundle.select_options') }}
                        </h3>
                        <button type="button" class="btn-close btn-close-white fs-3"
                                wire:click="$set('showModal', false)">
                        </button>
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
                                                <small class="text-muted">({{ __('bundle.item') }} {{ $i + 1 }})</small>
                                            </h5>

                                            @if (isset($colors[$bundleProduct->id]) && $colors[$bundleProduct->id]->isNotEmpty())
                                                <!-- Color Selection -->
                                                <div class="form-floating mb-4">
                                                    <select wire:model.live="selections.{{ $bundleProduct->id }}.{{ $i }}.color_id"
                                                            class="form-select form-control-lg border-primary shadow-sm">
                                                        <option value="">{{ __('bundle.select_color') }}</option>
                                                        @foreach ($colors[$bundleProduct->id] ?? [] as $color)
                                                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label class="fs-5">{{ __('bundle.color') }}</label>
                                                    @error("selections.{$bundleProduct->id}.{$i}.color_id")
                                                    <small class="text-danger fs-6">{{ $message }}</small>
                                                    @enderror
                                                </div>

                                                <!-- Size Selection -->
                                                <div class="form-floating mb-4">
                                                    <select wire:model.live="selections.{{ $bundleProduct->id }}.{{ $i }}.size_id"
                                                            class="form-select form-control-lg border-primary shadow-sm">
                                                        <option value="">{{ __('bundle.select_size') }}</option>
                                                        @foreach ($sizes[$bundleProduct->id][$i] ?? [] as $size)
                                                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label class="fs-5">{{ __('bundle.size') }}</label>
                                                    @error("selections.{$bundleProduct->id}.{$i}.size_id")
                                                    <small class="text-danger fs-6">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endfor
                            @endforeach

                            @if ($errors->has('cart_bundle_error'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('cart_bundle_error') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 🟡 Footer -->
                    <div class="modal-footer d-flex flex-column align-items-center px-5 py-4">
                        <div class="d-flex justify-content-between w-100">
                            <button class="btn btn-outline-secondary btn-lg rounded-pill d-flex align-items-center justify-content-center px-5 py-3 fs-5 fw-bold"
                                    wire:click="$set('showModal', false)"
                                    style="gap: 10px;">
                                <i class="fas fa-times"></i>
                                {{ __('bundle.cancel') }}
                            </button>

                            <button wire:click="addToCart"
                                    wire:loading.attr="disabled"
                                    wire:target="addToCart"
                                    class="btn btn-primary btn-lg rounded-pill d-flex align-items-center justify-content-center px-5 py-3 fs-5 fw-bold"
                                    style="gap: 10px;">
                                <i class="fas fa-cart-plus"></i>
                                {{ __('bundle.add_to_cart') }}
                                <span wire:loading wire:target="addToCart">
                <i class="fas fa-spinner fa-spin"></i>
            </span>
                            </button>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    @endif
</div>
