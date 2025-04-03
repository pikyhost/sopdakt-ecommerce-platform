<div>
    @if($products->count() > 0)
        <h2 class="mb-3">Products ({{ $products->count() }})</h2>
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="product-default">
                        <figure>
                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ $product->getFirstMediaUrl('feature_product_image') }}" width="280" height="280" alt="product">
                                <img src="{{ $product->getFirstMediaUrl('second_feature_product_image') }}" width="280" height="280" alt="product">
                            </a>
                        </figure>
                        <div class="product-details">
                            <div class="category-list">
                                <a href="{{ route('category.products', $product->category->slug ) }}" class="product-category">
                                    {{ $product->category->name }}
                                </a>
                            </div>
                            <h3 class="product-title">
                                <a href="{{ route('product.show', $product->slug) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <div class="ratings-container">
                                <div class="product-ratings">
                                    @php
                                        $ratingPercentage = ($product->average_rating / 5) * 100;
                                    @endphp
                                    <span class="ratings" style="width:{{ $ratingPercentage }}%"></span>
                                    <span class="tooltiptext tooltip-top">{{ number_format($product->average_rating, 1) }} / 5</span>
                                </div>
                            </div>
                            <div class="price-box">
                                @if ($product->after_discount_price)
                                    <del class="old-price">{{ $product->price_for_current_country }}</del>
                                @endif
                                <span class="product-price">{{ $product->discount_price_for_current_country }}</span>
                            </div>
                            <livewire:product-actions :product="$product" wire:key="search-product-{{ $product->id }}" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($categories->count() > 0)
        <h2 class="mb-3 mt-5">Categories ({{ $categories->count() }})</h2>
        <div class="row">
            @foreach($categories as $category)
                <div class="col-md-4 mb-4">
                    <div class="category-default">
                        <a href="{{ route('category.products', $category->slug) }}">
                            <figure>
                                @if($category->getFirstMediaUrl('category_image'))
                                    <img src="{{ $category->getFirstMediaUrl('category_image') }}" width="280" height="280" alt="category">
                                @else
                                    <img src="{{ asset('path/to/default/category/image.jpg') }}" width="280" height="280" alt="default category">
                                @endif
                            </figure>
                            <div class="category-details">
                                <h3 class="category-title">{{ $category->name }}</h3>
                                <span class="product-count">{{ $category->products_count }} products</span>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($products->count() == 0 && $categories->count() == 0)
        <div class="alert alert-info">
            No results found for "{{ $query }}"
        </div>
    @endif
        @livewire('product-compare')
</div>
