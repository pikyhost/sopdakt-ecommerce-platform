<div>
    <div>
        <div>
            <nav class="toolbox sticky-header horizontal-filter filter-sorts" data-sticky-options="{'mobile': true}">
                <div class="sidebar-overlay d-lg-none"></div>
                <a href="#" class="sidebar-toggle border-0">
                    <svg data-name="Layer 3" id="Layer_3" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <line x1="15" x2="26" y1="9" y2="9"></line>
                        <line x1="6" x2="9" y1="9" y2="9"></line>
                        <line x1="23" x2="26" y1="16" y2="16"></line>
                        <line x1="6" x2="17" y1="16" y2="16"></line>
                        <line x1="17" x2="26" y1="23" y2="23"></line>
                        <line x1="6" x2="11" y1="23" y2="23"></line>
                    </svg>
                    <span>Filter</span>
                </a>

                <aside class="toolbox-left sidebar-shop mobile-sidebar">
                    <!-- Size Filter -->
                    <div class="toolbox-item toolbox-sort select-custom">
                    <span class="sort-menu-trigger cursor-pointer" onclick="toggleDropdown('sizeFilter')">
                        Size
                    </span>
                        <ul class="sort-list dropdown" id="sizeFilter">
                            <li>
                                <button type="button" class="clear-btn" onclick="clearFilter('selectedSizes')">Clear All</button>
                            </li>
                            @foreach($sizes as $id => $name)
                                <li>
                                    <label>
                                        <input type="checkbox" wire:model.live="selectedSizes" value="{{ $id }}">
                                        {{ $name }}
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Color Filter -->
                    <div class="toolbox-item toolbox-sort select-custom">
                    <span class="sort-menu-trigger cursor-pointer" onclick="toggleDropdown('colorFilter')">
                        Color
                    </span>
                        <ul class="sort-list dropdown" id="colorFilter">
                            <li>
                                <button type="button" class="clear-btn" onclick="clearFilter('selectedColors')">Clear All</button>
                            </li>
                            @foreach($colors as $id => $name)
                                <li>
                                    <label>
                                        <input type="checkbox" wire:model.live="selectedColors" value="{{ $id }}">
                                        {{ $name }}
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="toolbox-item toolbox-sort price-sort select-custom">
                        <span class="sort-menu-trigger cursor-pointer" onclick="toggleDropdown('priceFilter')">Price</span>
                        <div class="sort-list dropdown" id="priceFilter">
                            <div class="filter-price-form d-flex align-items-center m-0">
                                <input type="number" wire:model.live="minPrice" class="input-price mr-2" placeholder="Min" />
                                -
                                <input type="number" wire:model.live="maxPrice" class="input-price mx-2" placeholder="Max" />
                            </div>
                            <button type="button" class="clear-btn mt-2" wire:click="resetPriceFilter">Clear Price</button>
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
                        <select wire:model="perPage" class="form-control">
                            <option value="1">Default (1)</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="40">40</option>
                            <option value="50">50</option>
                        </select>

                    </div><!-- End .select-custom -->
                </div><!-- End .toolbox-item -->


                <div class="toolbox-item layout-modes">
                    <a href="category.html" class="layout-btn btn-grid active" title="Grid">
                        <i class="icon-mode-grid"></i>
                    </a>
                    <a href="category-list.html" class="layout-btn btn-list" title="List">
                        <i class="icon-mode-list"></i>
                    </a>
                </div><!-- End .layout-modes -->
            </nav>
        </div>

        <div class="row">
            @foreach($products as $product)
                <div class="col-6 col-sm-4 col-md-3">
                    <div class="product-default">
                        <figure>
                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ $product->getFirstMediaUrl('feature_product_image') }}" width="74" height="74" alt="product">
                                <img src="{{ $product->getFirstMediaUrl('second_feature_product_image') }}" width="74" height="74" alt="product">
                            </a>
                        </figure>
                        <div class="product-details">
                            <h3 class="product-title">
                                <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                            </h3>
                            <div class="ratings-container">
                                <div class="product-ratings">
                                    <span class="ratings" style="width: {{ $product->getRatingPercentage() }}%"></span>
                                    <span class="tooltiptext tooltip-top"></span>
                                </div>
                            </div>
                            <div class="price-box">
                                @if ($product->after_discount_price)
                                    <del class="old-price">${{ $product->price_for_current_country }}</del>
                                @endif
                                <span class="product-price">${{ $product->discount_price_for_current_country }}</span>
                            </div>

                            <livewire:product-actions :product="$product" wire:key="product-{{ $product->id }}" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <nav class="toolbox toolbox-pagination">
            {{ $products->links() }}
        </nav>
    </div>

    <script>
        function toggleDropdown(id) {
            let dropdown = document.getElementById(id);
            let isVisible = dropdown.style.display === 'block';

            // Hide all dropdowns first
            document.querySelectorAll('.dropdown').forEach(el => el.style.display = 'none');

            // Toggle only the clicked dropdown
            dropdown.style.display = isVisible ? 'none' : 'block';
        }

        function clearFilter(filterName) {
            Livewire.emit('clearFilter', filterName); // Emit event to Livewire to clear the filter
        }

        document.addEventListener("DOMContentLoaded", () => {
            document.addEventListener("click", function (event) {
                let isDropdown = event.target.closest(".toolbox-item");

                if (!isDropdown) {
                    document.querySelectorAll('.dropdown').forEach(el => el.style.display = 'none');
                }
            });

            // Ensure dropdowns stay open after Livewire updates
            Livewire.hook('message.processed', (message, component) => {
                document.querySelectorAll('.dropdown').forEach(el => {
                    if (el.dataset.keepOpen === "true") {
                        el.style.display = 'block';
                    }
                });
            });
        });
    </script>

    <style>
        .dropdown {
            display: none;
            position: absolute;
            background: white;
            border: 1px solid #ccc;
            padding: 10px;
            list-style: none;
            z-index: 100;
        }

        .cursor-pointer { cursor: pointer; }

        .clear-btn {
            display: block;
            width: 100%;
            background: #f44336;
            color: white;
            border: none;
            padding: 5px;
            cursor: pointer;
            text-align: center;
        }

        .clear-btn:hover { background: #d32f2f; }
    </style>
</div>
