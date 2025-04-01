<div class="row align-items-lg-stretch">
    <!-- Sidebar -->
    <aside class="filter-sidebar col-lg-2">
        <div class="sidebar-wrapper">
            <h3 class="ls-n-10 text-uppercase text-primary">Sort By</h3>
            <ul class="check-filter-list">
                <li>
                    <a href="#" wire:click.prevent="filterByCategory('all')"
                       class="{{ $selectedCategorySlug === 'all' ? 'active' : '' }}">
                        All
                    </a>
                </li>
                @foreach($categories as $slug => $name)
                    <li>
                        <a href="#" wire:click.prevent="filterByCategory('{{ $slug }}')"
                           class="{{ $selectedCategorySlug === $slug ? 'active' : '' }}">
                            {{ ucfirst($name) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </aside>

    <!-- Products Section -->
    <div class="col-lg-10">
        <div class="row product-ajax-grid mb-2">
            @forelse($products as $product)
                <div class="col-6 col-md-4 col-lg-3 col-xl-5col">
                    <div class="product-default inner-quickview inner-icon">
                        <figure>
                            <a href="{{ route('product.show', $product['slug']) }}">
                                <img src="{{ $product['image'] }}" width="205" height="205" alt="product">
                            </a>
                            <div class="btn-icon-group">
                                @livewire('add-to-cart-home-page', ['product' => $product['id']])
                            </div>
                            <!-- Add to Compare -->
                            @livewire('add-to-compare', ['product' => $product['id']])
                        </figure>
                        <div class="product-details">
                            <div class="category-wrap">
                                <div class="category-list">
                                    <a href="{{ route('category.products', $product['category_slug']) }}"
                                       class="product-category">
                                        {{ $product['category_name'] }}
                                    </a>
                                </div>
                                @livewire('love-button-home-page', ['product' => $product['id']], key('love-' . $product['id']))
                            </div>
                            <h3 class="product-title">
                                <a href="{{ route('product.show', $product['slug']) }}">{{ $product['name'] }}</a>
                            </h3>
                            <div class="ratings-container">
                                <div class="product-ratings">
                                    <span class="ratings" style="width: {{ $product['rating_percentage'] }}%"></span>
                                </div>
                            </div>
                            <div class="price-box">
                                @isset($product['discount_price'])
                                    <span class="product-price">{{ $product['discount_price'] }}</span>
                                @else
                                    <span class="product-price">{{ $product['original_price'] }}</span>
                                @endisset
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p>No products found.</p>
                </div>
            @endforelse
        </div>

        <div class="product-more-container d-flex justify-content-center">
            <a href="{{ $selectedCategorySlug !== 'all' ? route('category.products', ['slug' => $selectedCategorySlug]) : route('products') }}"
               onclick="event.stopPropagation();"
               class="btn btn-outline-dark">
                Load More...
            </a>
        </div>
    </div>
</div>
