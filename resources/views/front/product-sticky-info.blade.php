@extends('layouts.app')

@section('title', 'view product')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ url('/products') }}">Products</a></li>
            </ol>
        </nav>

        <div class="product-single-container product-single-info">
            <div class="cart-message d-none">
                <strong class="single-cart-notice">“{{ $product->getTranslation('name', app()->getLocale()) }}”</strong>
                <span>has been added to your cart.</span>
            </div>

            <div class="row">
                <div class="col-lg-5 col-md-6 product-single-gallery">
                    @if ($product->getFeatureProductImageUrl())
                        <div class="product-item position-relative">
                            <div class="inner position-relative">

                                <!-- Labels Group (Similar Style as "HOT" and "SALE") -->
                                <div class="label-group position-absolute" style="top: 10px; left: 10px; z-index: 10;">
                                    @forelse($product->labels as $label)
                                        <div class="product-label"
                                             style="background-color: {{ $label->background_color_code }};
                                color: {{ $label->color_code }};
                                font-size: 14px;
                                font-weight: bold;">
                                            {{ $label->getTranslation('title', app()->getLocale()) }}
                                        </div>
                                    @empty
                                        <!-- No Labels -->
                                    @endforelse
                                </div>

                                <!-- Product Image -->
                                <img src="{{ $product->getFeatureProductImageUrl() }}"
                                     data-zoom-image="{{ $product->getFeatureProductImageUrl() }}"
                                     width="480" height="480" alt="feature-image">

                                <!-- Full Screen Icon -->
                                <span class="prod-full-screen">
                <i class="icon-plus"></i>
            </span>
                            </div>
                        </div><!-- End .product-item -->
                    @endif


                    @if ($product->getSecondFeatureProductImageUrl())
                        <div class="product-item">
                            <div class="inner">
                                <img src="{{ $product->getSecondFeatureProductImageUrl() }}"
                                     data-zoom-image="{{ $product->getSecondFeatureProductImageUrl() }}"
                                     width="480" height="480" alt="second-feature-image">
                                <span class="prod-full-screen">
                    <i class="icon-plus"></i>
                </span>
                            </div>
                        </div><!-- End .product-item -->
                    @endif

                    @foreach (array_reverse($product->getMoreProductImagesAndVideosUrls()) as $mediaUrl)
                        <div class="product-item">
                            <div class="inner">
                                @if (Str::endsWith($mediaUrl, ['.mp4', '.mpeg', '.mov', '.avi']))
                                    <video width="480" height="480" controls>
                                        <source src="{{ $mediaUrl }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <img src="{{ $mediaUrl }}" data-zoom-image="{{ $mediaUrl }}"
                                         width="480" height="480" alt="product-media">
                                @endif
                                <span class="prod-full-screen">
                <i class="icon-plus"></i>
            </span>
                            </div>
                        </div><!-- End .product-item -->
                    @endforeach

                </div><!-- End .col-md-5 -->


                <div class="col-lg-7 col-md-6">
                    <div class="sidebar-wrapper">
                        <div class="product-single-details">
                            <h1 class="product-title">{{ $product->name }}</h1>

                            <div class="ratings-container">
                                <div class="product-ratings">
                                    <span class="ratings" style="width: {{ $product->fake_average_rating * 20 }}%"></span><!-- End .ratings -->
                                    <span class="tooltiptext tooltip-top"></span>
                                </div><!-- End .product-ratings -->

                                <a href="#" class="rating-link">( {{ $product->ratings()->count() }} Reviews )</a>
                            </div><!-- End .ratings-container -->

                            <hr class="short-divider">

                            <div class="price-box">
                                @if($product->after_discount_price)
                                    <span class="old-price">{{ $product->price_for_current_country }}</span>
                                    <span class="new-price">{{ $product->discount_price_for_current_country }}</span>
                                @else
                                    <span class="new-price">{{ $product->price_for_current_country }}</span>
                                @endif
                            </div><!-- End .price-box -->

                            <div class="product-desc">
                                <p>
                                    {{ $product->summary }}
                                </p>
                            </div><!-- End .product-desc -->

                            <ul class="single-info-list">
                                <!---->
                                <li>
                                    SKU:
                                    <strong>
                                        {{ $product->sku }}
                                    </strong>
                                </li>

                                <li>
                                    CATEGORY:
                                    <strong>
                                        <a href="{{ route('category.products', $product->category->slug) }}" class="product-category">
                                            {{ $product->category->name }}
                                        </a>
                                    </strong>
                                </li>
                            </ul>

                            @if($product->productColors->isNotEmpty())
                                <div class="product-filters-container custom-product-filters">
                                    <div>
                                        <div class="product-single-filter">
                                            <label>Size:</label>
                                            <ul class="config-size-list">
                                                @php
                                                    $sizeIds = collect();
                                                    foreach ($product->productColors as $productColor) {
                                                        $sizeIds = $sizeIds->merge($productColor->sizes->pluck('id'));
                                                    }
                                                    $sizes = \App\Models\Size::whereIn('id', $sizeIds->unique())->get();
                                                @endphp
                                                @foreach($sizes as $size)
                                                    <li>
                                                        <a class="d-flex align-items-center justify-content-center">{{ $size->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    @endif
                                    <br>

                                    <!-- Color Selection -->
                                    @if($product->productColors->isNotEmpty())
                                        <div class="product-single-filter">
                                            <label>{{ __('Color:') }}</label>
                                            <ul class="config-size-list config-color-list">
                                                @foreach($product->productColors as $productColor)
                                                    <li>
                                                        <a href="javascript:;"
                                                           class="d-flex align-items-center justify-content-center p-0 color-swatch"
                                                           style="background-color: {{ $productColor->color->code }}; border: 2px solid #ddd;"
                                                           title="{{ $productColor->color->name }}"
                                                           data-image="{{ asset('storage/' . $productColor->image) }}">
                                                            &nbsp;
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                </div>


                                {{-- Display the selected color image --}}
                                <div class="selected-color-image mt-3">
                                    <img id="color-image" src="" alt="Selected Color" style="display: none; width: 200px; height: auto;">
                                </div>

                                <script>
                                    document.addEventListener("DOMContentLoaded", function () {
                                        const colorSwatches = document.querySelectorAll(".color-swatch");
                                        const colorImage = document.getElementById("color-image");

                                        colorSwatches.forEach(swatch => {
                                            swatch.addEventListener("click", function () {
                                                const imageUrl = this.getAttribute("data-image");

                                                if (imageUrl) {
                                                    colorImage.src = imageUrl;
                                                    colorImage.style.display = "block";
                                                }
                                            });
                                        });
                                    });
                                </script>


                                {{-- Inline CSS for styling --}}
                                <style>
                                    .config-color-list {
                                        list-style: none;
                                        padding: 0;
                                        display: flex;
                                        gap: 8px;
                                    }

                                    .color-swatch {
                                        width: 30px;
                                        height: 30px;
                                        display: block;
                                        border-radius: 50%; /* Round shape */
                                        transition: transform 0.2s ease, border-color 0.2s ease;
                                    }

                                    .color-swatch:hover {
                                        transform: scale(1.1);
                                        border-color: #666; /* Darker border on hover */
                                    }
                                </style>
                                <!---->
                        </div>

                        @livewire('add-to-cart', ['productId' => $product->id])

                        <hr class="divider mb-0 mt-0">

                        <livewire:wishlist-button :product-id="$product->id" />

                        @livewire('add-bundle-to-cart', ['product' => $product])

                        @if (session()->has('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                    </div><!-- End .product-single-details -->
                </div>
            </div><!-- End .col-md-7 -->
        </div>
    </div><!-- End .product-single-container -->

    <div class="product-single-tabs custom-product-single-tabs bg-gray mb-4">
        <div class="container">
            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="product-tab-desc" data-toggle="tab" href="#product-desc-content" role="tab"
                       aria-controls="product-desc-content" aria-selected="true">Description</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="product-tab-size" data-toggle="tab" href="#product-size-content" role="tab"
                       aria-controls="product-size-content" aria-selected="true">Size Guide</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="product-tab-reviews" data-toggle="tab" href="#product-reviews-content" role="tab"
                       aria-controls="product-reviews-content" aria-selected="false">
                        Reviews @if($product->reviews_count) ({{ $product->reviews_count }}) @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="product-tab-tags" data-toggle="tab" href="#product-tags-content" role="tab"
                       aria-controls="product-tags-content" aria-selected="false">Custom Tab</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="product-desc-content" role="tabpanel"
                     aria-labelledby="product-tab-desc">
                    <div class="product-desc-content">
                        <p>{{ $product->description }}</p>
                    </div><!-- End .product-desc-content -->
                </div><!-- End .tab-pane -->

                <!-- Size Guide Tab -->
                <div class="tab-pane fade" id="product-size-content" role="tabpanel"
                     aria-labelledby="product-tab-size">
                    <div class="product-size-content">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="{{ $product->getProductSizeImageUrl() ?? asset('assets/images/products/default-size.png') }}"
                                     alt="Size Guide" class="img-fluid">
                            </div><!-- End .col-md-4 -->
                        </div><!-- End .row -->
                    </div><!-- End .product-size-content -->
                </div><!-- End .tab-pane -->

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="product-reviews-content" role="tabpanel"
                     aria-labelledby="product-tab-reviews">
                    <div class="product-reviews-content">
                        @livewire('product-reviews', ['product' => $product])
                    </div><!-- End .product-reviews-content -->
                </div><!-- End .tab-pane -->

                <!-- Custom Tab -->
                <div class="tab-pane fade" id="product-tags-content" role="tabpanel"
                     aria-labelledby="product-tab-tags">
                    <h4>Product Features and Attributes</h4>

                    @if (!empty($customAttributes))
                        <ul>
                            @foreach ($customAttributes[app()->getLocale()] ?? [] as $key => $value)
                                <li><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</li>
                            @endforeach
                        </ul>
                    @endif

                    @if ($product->attributes->isNotEmpty())
                        <ul>
                            @foreach ($product->attributes as $attribute)
                                <li>
                                    <strong>{{ $attribute->getTranslation('name', app()->getLocale()) }}</strong>:
                                    {{ $attribute->pivot->value }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if ($product->types->isNotEmpty())
                        <h4>Available Types</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-3">
                            @foreach ($product->types as $type)
                                <div class="text-center">
                                    <img src="{{ asset('storage/' . $type->image) }}"
                                         alt="{{ $type->name }}" class="img-fluid mb-2 rounded-lg shadow-md">
                                    <p>{{ $type->name }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div><!-- End .tab-pane -->
            </div><!-- End .tab-content -->
        </div>
    </div><!-- End .product-single-tabs -->

    <div class="container">
        <div class="products-section pt-0">
            <h2 class="section-title">Related Products</h2>
            <div class="products-slider owl-carousel owl-theme dots-top dots-small">
                @foreach ($relatedProducts as $relatedProduct)
                    <div class="product-default">
                        <figure>
                            <a href="{{ route('product.show', $relatedProduct->slug) }}">
                                <img src="{{ $relatedProduct->getFirstMediaUrl('feature_product_image') }}" width="280" height="280" alt="product">
                                <img src="{{ $relatedProduct->getFirstMediaUrl('second_feature_product_image') }}" width="280" height="280" alt="product">
                            </a>
                        </figure>
                        <div class="product-details">
                            <div class="category-list">
                                <a href="{{ route('category.products',$relatedProduct->category->slug ) }}" class="product-category">
                                    {{ $relatedProduct->category->name }}
                                </a>
                            </div>
                            <h3 class="product-title">
                                <a href="{{ route('product.show', $relatedProduct->slug) }}">
                                    {{ $relatedProduct->name }}
                                </a>
                            </h3>
                            <div class="ratings-container">
                                <div class="product-ratings">
                                    @php
                                        $ratingPercentage = ($relatedProduct->average_rating / 5) * 100;
                                    @endphp
                                    <span class="ratings" style="width:{{ $ratingPercentage }}%"></span>
                                    <span class="tooltiptext tooltip-top">{{ number_format($relatedProduct->average_rating, 1) }} / 5</span>
                                </div>
                            </div>
                            <div class="price-box">
                                @if ($relatedProduct->after_discount_price)
                                    <del class="old-price">{{ $relatedProduct->price_for_current_country }}</del>
                                @endif
                                <span class="product-price">{{ $relatedProduct->discount_price_for_current_country }}</span>
                            </div>
                            <livewire:product-actions :product="$relatedProduct" wire:key="relatedProduct-{{ $product->id }}" />
                        </div>
                    </div>
                @endforeach
            </div>
            @livewire('product-compare')
        </div><!-- End .products-section -->

        <hr class="mt-0 m-b-5" />

        <div class="product-widgets-container row pb-2">
            <div class="col-lg-3 col-sm-6 pb-5 pb-md-0">
                <h4 class="section-sub-title">Featured Products</h4>

                @foreach($featuredProducts as $featuredProduct)
                    <div class="product-default left-details product-widget">
                        <figure>
                            <a href="{{ route('product.show', $featuredProduct->slug) }}">
                                <img src="{{ $featuredProduct->getFirstMediaUrl('feature_product_image') }}"
                                     width="74" height="74" alt="product">
                                <img src="{{ $featuredProduct->getFirstMediaUrl('second_feature_product_image') }}"
                                     width="74" height="74" alt="product">
                            </a>
                        </figure>

                        <div class="product-details">
                            <h3 class="product-title">
                                <a href="{{ route('product.show', $featuredProduct->slug) }}">
                                    {{ $featuredProduct->name }}
                                </a>
                            </h3>

                            <div class="ratings-container">
                                <div class="product-ratings">
                                    <span class="ratings" style="width: {{ $featuredProduct->getRatingPercentage() }}%"></span>
                                    <span class="tooltiptext tooltip-top"></span>
                                </div>
                            </div>

                            <div class="price-box">
                                <span class="product-price">{{ $featuredProduct->discount_price_for_current_country }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


            <div class="col-lg-3 col-sm-6 pb-5 pb-md-0">
                <h4 class="section-sub-title">Best Selling Products</h4>

                @foreach($bestSellingProducts as $bestProduct)
                    <div class="product-default left-details product-widget">
                        <figure>
                            <a href="{{ route('product.show', $bestProduct->slug) }}">
                                <img src="{{ $bestProduct->getFirstMediaUrl('feature_product_image') }}"
                                     width="74" height="74" alt="product">
                                <img src="{{ $bestProduct->getFirstMediaUrl('second_feature_product_image') }}"
                                     width="74" height="74" alt="product">
                            </a>
                        </figure>

                        <div class="product-details">
                            <h3 class="product-title">
                                <a href="{{ route('product.show', $bestProduct->slug) }}">
                                    {{ $bestProduct->name }}
                                </a>
                            </h3>

                            <div class="ratings-container">
                                <div class="product-ratings">
                                    <span class="ratings" style="width: {{ $bestProduct->getRatingPercentage() }}%"></span>
                                    <span class="tooltiptext tooltip-top">{{ number_format($bestProduct->getRatingPercentage(), 2) }}</span>
                                </div>
                            </div>

                            <div class="price-box">
                                <span class="product-price">{{ $bestProduct->discount_price_for_current_country }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-lg-3 col-sm-6 pb-5 pb-md-0">
                <h4 class="section-sub-title">Latest Products</h4>

                @foreach($latestProducts as $latestProduct)
                    <div class="product-default left-details product-widget">
                        <figure>
                            <a href="{{ route('product.show', $latestProduct->slug) }}">
                                <img src="{{ $latestProduct->getFirstMediaUrl('feature_product_image') }}"
                                     width="74" height="74" alt="product">
                                <img src="{{ $latestProduct->getFirstMediaUrl('second_feature_product_image') }}"
                                     width="74" height="74" alt="product">
                            </a>
                        </figure>

                        <div class="product-details">
                            <h3 class="product-title">
                                <a href="{{ route('product.show', $latestProduct->slug) }}">
                                    {{ $latestProduct->name }}
                                </a>
                            </h3>

                            <div class="ratings-container">
                                <div class="product-ratings">
                                    <span class="ratings" style="width: {{ $latestProduct->getRatingPercentage() }}%"></span>
                                    <span class="tooltiptext tooltip-top">{{ number_format($latestProduct->getRatingPercentage(), 2) }}</span>
                                </div>
                            </div>

                            <div class="price-box">
                                @if ($latestProduct->after_discount_price)
                                    <del class="old-price">{{ $latestProduct->price_for_current_country }}</del>
                                @endif
                                <span class="product-price">{{ $latestProduct->discount_price_for_current_country }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-lg-3 col-sm-6 pb-5 pb-md-0">
                <h4 class="section-sub-title">Top Rated Products</h4>

                @foreach($topRatedProducts as $topRatedProduct)
                    <div class="product-default left-details product-widget">
                        <figure>
                            <a href="{{ route('product.show', $topRatedProduct->slug) }}">
                                <img src="{{ $topRatedProduct->getFirstMediaUrl('feature_product_image') }}"
                                     width="74" height="74" alt="product">
                                <img src="{{ $topRatedProduct->getFirstMediaUrl('second_feature_product_image') }}"
                                     width="74" height="74" alt="product">
                            </a>
                        </figure>

                        <div class="product-details">
                            <h3 class="product-title">
                                <a href="{{ route('product.show', $topRatedProduct->slug) }}">
                                    {{ $topRatedProduct->name }}
                                </a>
                            </h3>

                            <div class="ratings-container">
                                <div class="product-ratings">
                                    <span class="ratings" style="width: {{ ($topRatedProduct->final_average_rating / 5) * 100 }}%"></span>
                                    <span class="tooltiptext tooltip-top">{{ number_format($topRatedProduct->final_average_rating, 2) }}</span>
                                </div>
                            </div>

                            <div class="price-box">
                                <span class="product-price">{{ $topRatedProduct->discount_price_for_current_country }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div><!-- End .row -->
    </div>
@endsection

@push('styles')
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Custom Styles -->
    <style>
        .product-single-tabs {
            padding: 40px 0;
        }

        .nav-tabs .nav-link {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            border: none;
        }

        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
        }

        .tab-content {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
        }

        .tab-pane img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
    <!-- Bootstrap CSS (only for product page) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <!-- Bootstrap JS (only for product page) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
