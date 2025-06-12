<div>
    <div class="search-results-container">
        @if($products->count() > 0)
            <h2 class="results-section-title">Products <span class="results-count">{{ $products->count() }}</span></h2>
            <div class="product-grid">
                @foreach($products as $product)
                    <div class="product-card">
                        <figure class="product-image-container">
                            <a href="{{ route('product.show', $product->slug) }}" class="product-image-link">
                                <img src="{{ $product->getFirstMediaUrl('feature_product_image') }}"
                                     alt="{{ $product->name }}"
                                     class="product-image primary-image"
                                     loading="lazy">
                                <img src="{{ $product->getFirstMediaUrl('second_feature_product_image') }}"
                                     alt="{{ $product->name }}"
                                     class="product-image secondary-image"
                                     loading="lazy">
                            </a>
                        </figure>
                        <div class="product-info">
                            <div class="product-category">
                                <a href="{{ route('category.products', $product->category->slug ) }}" class="category-link">
                                    {{ $product->category->name }}
                                </a>
                            </div>
                            <h3 class="product-name">
                                <a href="{{ route('product.show', $product->slug) }}" class="product-link">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <div class="product-rating">
                                <div class="rating-stars">
                                    <div class="rating-fill" style="width: {{ ($product->average_rating / 5) * 100 }}%"></div>
                                </div>
                                <span class="rating-value">{{ number_format($product->average_rating, 1) }}/5</span>
                            </div>
                            <div class="product-pricing">
                                @if ($product->after_discount_price)
                                    <span class="original-price">{{ $product->price_for_current_country }}</span>
                                @endif
                                <span class="current-price">{{ $product->discount_price_for_current_country }}</span>
                            </div>
                            <livewire:product-actions :product="$product" wire:key="search-product-{{ $product->id }}" />
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($categories->count() > 0)
            <h2 class="results-section-title">Categories <span class="results-count">{{ $categories->count() }}</span></h2>
            <div class="category-grid">
                @foreach($categories as $category)
                    <div class="category-card">
                        <a href="{{ route('category.products', $category->slug) }}" class="category-link">
                            <figure class="category-image-container">
                                @if($category->getFirstMediaUrl('main_category_image'))
                                    <img src="{{ $category->getFirstMediaUrl('main_category_image') }}"
                                         alt="{{ $category->name }}"
                                         class="category-image"
                                         loading="lazy">
                                @else
                                    <img src="{{ asset('path/to/default/category/image.jpg') }}"
                                         alt="{{ $category->name }}"
                                         class="category-image"
                                         loading="lazy">
                                @endif
                            </figure>
                            <div class="category-info">
                                <h3 class="category-name">{{ $category->name }}</h3>
                                <span class="product-count">{{ $category->products_count }} products</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        @if($products->count() == 0 && $categories->count() == 0)
            <div class="no-results-message">
                <i class="fas fa-search"></i>
                <h3>No results found for "{{ $query }}"</h3>
                <p>Try different search terms or browse our categories</p>
            </div>
        @endif

        @livewire('product-compare')
    </div>

    <style>
        /* Base Styles */
        .search-results-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            font-family: 'Segoe UI', Roboto, sans-serif;
        }

        /* Typography */
        .results-section-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #000;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .results-count {
            background: #000;
            color: #fff;
            font-size: 1rem;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
        }

        /* Product Grid */
        .product-grid, .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        /* Product Card */
        .product-card {
            background: #fff;
            border: 1px solid #eaeaea;
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .product-card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            transform: translateY(-5px);
        }

        .product-image-container {
            position: relative;
            margin: 0;
            overflow: hidden;
            height: 280px;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 0.3s ease;
        }

        .secondary-image {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
        }

        .product-image-link:hover .primary-image {
            opacity: 0;
        }

        .product-image-link:hover .secondary-image {
            opacity: 1;
        }

        .product-info {
            padding: 1.25rem;
        }

        .category-link {
            color: #666;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s;
        }

        .category-link:hover {
            color: #000;
        }

        .product-name {
            margin: 0.5rem 0;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .product-link {
            color: #000;
            text-decoration: none;
            transition: color 0.2s;
        }

        .product-link:hover {
            color: #444;
        }

        /* Rating */
        .product-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.75rem 0;
        }

        .rating-stars {
            position: relative;
            display: inline-block;
            width: 80px;
            height: 16px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='%23e0e0e0'%3E%3Cpath d='M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.967-7.417 3.967 1.481-8.279-6.064-5.828 8.332-1.151z'/%3E%3C/svg%3E");
            background-size: 16px;
        }

        .rating-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='%23000'%3E%3Cpath d='M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.967-7.417 3.967 1.481-8.279-6.064-5.828 8.332-1.151z'/%3E%3C/svg%3E");
            background-size: 16px;
        }

        .rating-value {
            font-size: 0.875rem;
            color: #666;
        }

        /* Pricing */
        .product-pricing {
            margin: 1rem 0;
        }

        .current-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #000;
        }

        .original-price {
            font-size: 1rem;
            color: #999;
            text-decoration: line-through;
            margin-right: 0.5rem;
        }

        /* Category Cards */
        .category-card {
            background: #fff;
            border: 1px solid #eaeaea;
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .category-card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            transform: translateY(-5px);
        }

        .category-image-container {
            margin: 0;
            height: 200px;
            overflow: hidden;
        }

        .category-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .category-link:hover .category-image {
            transform: scale(1.05);
        }

        .category-info {
            padding: 1.25rem;
            text-align: center;
        }

        .category-name {
            margin: 0 0 0.25rem;
            font-size: 1.125rem;
            font-weight: 600;
            color: #000;
        }

        .product-count {
            font-size: 0.875rem;
            color: #666;
        }

        /* No Results */
        .no-results-message {
            text-align: center;
            padding: 3rem 0;
        }

        .no-results-message i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 1.5rem;
        }

        .no-results-message h3 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .no-results-message p {
            color: #666;
            font-size: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .product-grid, .category-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1.5rem;
            }

            .product-image-container, .category-image-container {
                height: 220px;
            }
        }
    </style>
</div>
