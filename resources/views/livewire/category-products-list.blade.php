<div>
    <nav class="toolbox sticky-header horizontal-filter filter-sorts" data-sticky-options="{'mobile': true}">
        <div class="sidebar-overlay d-lg-none"></div>
        <a href="#" class="sidebar-toggle border-0">
            <svg data-name="Layer 3" id="Layer_3" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                <line x1="15" x2="26" y1="9" y2="9" class="cls-1"></line>
                <line x1="6" x2="9" y1="9" y2="9" class="cls-1"></line>
                <line x1="23" x2="26" y1="16" y2="16" class="cls-1"></line>
                <line x1="6" x2="17" y1="16" y2="16" class="cls-1"></line>
                <line x1="17" x2="26" y1="23" y2="23" class="cls-1"></line>
                <line x1="6" x2="11" y1="23" y2="23" class="cls-1"></line>
                <path d="M14.5,8.92A2.6,2.6,0,0,1,12,11.5,2.6,2.6,0,0,1,9.5,8.92a2.5,2.5,0,0,1,5,0Z" class="cls-2"></path>
                <path d="M22.5,15.92a2.5,2.5,0,1,1-5,0,2.5,2.5,0,0,1,5,0Z" class="cls-2"></path>
                <path d="M21,16a1,1,0,1,1-2,0,1,1,0,0,1,2,0Z" class="cls-3"></path>
                <path d="M16.5,22.92A2.6,2.6,0,0,1,14,25.5a2.6,2.6,0,0,1-2.5-2.58,2.5,2.5,0,0,1,5,0Z" class="cls-2"></path>
            </svg>
            <span>Filter</span>
        </a>

        <aside class="toolbox-left sidebar-shop mobile-sidebar">
            <div class="toolbox-item toolbox-sort select-custom">
                <span class="sort-menu-trigger">Size</span>
                <ul class="sort-list">
                    @foreach($sizes as $id => $name)
                        <li>
                            <label>
                                <input type="checkbox" wire:model.live="selectedSizes" value="{{ $id }}"> {{ $name }}
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="toolbox-item toolbox-sort select-custom">
                <span class="sort-menu-trigger">Color</span>
                <ul class="sort-list">
                    @foreach($colors as $id => $name)
                        <li>
                            <label>
                                <input type="checkbox" wire:model.live="selectedColors" value="{{ $id }}"> {{ $name }}
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="toolbox-item toolbox-sort price-sort select-custom">
                <span class="sort-menu-trigger">Price</span>
                <div class="sort-list">
                    <form class="filter-price-form d-flex align-items-center m-0">
                        <input class="input-price mr-2" wire:model.live="minPrice" placeholder="Min" /> -
                        <input class="input-price mx-2" wire:model.live="maxPrice" placeholder="Max" />
                        <button type="button" wire:click="$refresh" class="btn btn-primary ml-3">Filter</button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="toolbox-item toolbox-sort select-custom">
            <select wire:model.live="sortBy" class="form-control">
                <option value="latest">Default sorting</option>
                <option value="popularity">Sort by popularity</option>
                <option value="rating">Sort by average rating</option>
                <option value="date">Sort by newness</option>
                <option value="price_asc">Sort by price: low to high</option>
                <option value="price_desc">Sort by price: high to low</option>
            </select>
        </div>

        <div class="toolbox-item toolbox-show ml-auto">
            <label>Show:</label>
            <div class="select-custom">
                <select wire:model.live="perPage" class="form-control">
                    <option value="1">Default (1)</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        <div class="toolbox-item layout-modes">
            <a href="#" class="layout-btn btn-grid active" title="Grid">
                <i class="icon-mode-grid"></i>
            </a>
            <a href="#" class="layout-btn btn-list" title="List">
                <i class="icon-mode-list"></i>
            </a>
        </div>
    </nav>

    <div class="row">
        @foreach($products as $product)
            <div class="col-6 col-sm-4 col-md-3">
                <div class="product-default">
                    <figure>
                        <a href="{{ route('product.show', $product->slug) }}">
                            <img src="{{ $product->getFirstMediaUrl('feature_product_image') }}" alt="product">
                            <img src="{{ $product->getFirstMediaUrl('second_feature_product_image') }}" alt="product">
                        </a>
                    </figure>
                    <div class="product-details">
                        <h3 class="product-title">
                            <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                        </h3>
                        <div class="ratings-container">
                            <div class="product-ratings">
                                <span class="ratings" style="width: {{ $product->getRatingPercentage() }}%"></span>
                            </div>
                        </div>
                        <div class="price-box">
                            @if ($product->after_discount_price)
                                <del class="old-price">${{ $product->price_for_current_country }}</del>
                            @endif
                            <span class="product-price">${{ $product->discount_price_for_current_country }}</span>
                        </div>
                        <livewire:product-actions :product="$relatedProduct" wire:key="relatedProduct-{{ $product->id }}" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <nav class="toolbox-pagination">
        {{ $products->links() }}
    </nav>
</div>
