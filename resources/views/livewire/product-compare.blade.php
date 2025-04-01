<div>
    <div class="compare-products-section">
        @if(count($compareProducts) > 0)
            <div class="compare-products-container">
                <!-- Header with counter and actions -->
                <div class="compare-header">
                    <div class="compare-title-wrapper">
                        <h3 class="compare-title">Product Comparison <span class="compare-count">({{ count($compareProducts) }})</span></h3>
                    </div>
                    <div class="compare-actions">
                        <button wire:click="clearCompare" class="compare-clear-btn">
                            <svg class="clear-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 7L18.1327 19.1425C18.0579 20.1891 17.187 21 16.1378 21H7.86224C6.81296 21 5.94208 20.1891 5.86732 19.1425L5 7M10 11V17M14 11V17M15 7V4C15 3.44772 14.5523 3 14 3H10C9.44772 3 9 3.44772 9 4V7M4 7H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Clear All
                        </button>
                    </div>
                </div>

                <!-- Products grid with horizontal scrolling -->
                <div class="compare-products-scroller">
                    <div class="compare-products-grid">
                        @foreach($compareProducts as $product)
                            <div class="compare-product-card">
                                <button wire:click="removeFromCompare({{ $product->id }})"
                                        class="compare-remove-btn"
                                        aria-label="Remove {{ $product->name }} from comparison">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>

                                <div class="product-image-container">
                                    <img src="{{ $product->getFirstMediaUrl('feature_product_image') }}"
                                         alt="{{ $product->name }}"
                                         loading="lazy"
                                         class="product-image"
                                         width="120"
                                         height="120">
                                </div>

                                <h4 class="product-name">{{ $product->name }}</h4>

                                <div class="product-price">
                                    @php
                                        $currency = \App\Models\Setting::getCurrency();
                                        $symbol = $currency?->code ?? '';
                                        $locale = app()->getLocale();
                                        $price = $product->after_discount_price ?? $product->price;
                                    @endphp
                                    <span class="price-amount">{{ number_format($price, 2) }}</span>
                                    <span class="price-currency">{{ $symbol }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Compare action button -->
                @if(count($compareProducts) > 1)
                    <div class="compare-footer">
                        <a href="{{ route('compare.products', ['ids' => implode(',', $compareProducts->pluck('id')->toArray())]) }}"
                           class="compare-action-btn">
                            <svg class="compare-action-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 17L9 11L13 15L21 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15 7H21V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Compare Products
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <style>
        /* Base styles */
        .compare-products-section {
            --primary-color: #2b5876;
            --secondary-color: #4e4376;
            --accent-color: #4fc3f7;
            --text-color: #2d3748;
            --light-gray: #f8fafc;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 2rem 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* Container styles */
        .compare-products-container {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        /* Header styles */
        .compare-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .compare-title-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .compare-icon {
            width: 24px;
            height: 24px;
            color: white;
        }

        .compare-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            line-height: 1.5;
        }

        .compare-count {
            font-weight: 400;
            opacity: 0.9;
        }

        /* Clear button */
        .compare-clear-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        .compare-clear-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .clear-icon {
            width: 16px;
            height: 16px;
        }

        /* Products scroller */
        .compare-products-scroller {
            overflow-x: auto;
            padding: 1.5rem;
            -webkit-overflow-scrolling: touch;
        }

        .compare-products-scroller::-webkit-scrollbar {
            height: 6px;
        }

        .compare-products-scroller::-webkit-scrollbar-track {
            background: var(--light-gray);
            border-radius: 3px;
        }

        .compare-products-scroller::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 3px;
        }

        /* Products grid */
        .compare-products-grid {
            display: flex;
            gap: 1.25rem;
            padding-bottom: 0.5rem;
        }

        /* Product card */
        .compare-product-card {
            position: relative;
            min-width: 180px;
            max-width: 200px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1.25rem;
            transition: var(--transition);
            background: white;
            box-shadow: var(--shadow-sm);
        }

        .compare-product-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-4px);
            border-color: var(--accent-color);
        }

        /* Remove button */
        .compare-remove-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #ff4444;
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .compare-remove-btn:hover {
            background: #ff0000;
            transform: scale(1.1);
        }

        /* Product image */
        .product-image-container {
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .product-image {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            mix-blend-mode: multiply;
            filter: saturate(1.1);
        }

        /* Product name */
        .product-name {
            font-size: 0.95rem;
            margin: 0 0 0.75rem;
            color: var(--text-color);
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.4;
        }

        /* Product price */
        .product-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
        }

        .price-amount {
            font-weight: 800;
        }

        .price-currency {
            font-size: 0.9em;
            opacity: 0.9;
        }

        /* Footer with compare button */
        .compare-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid var(--border-color);
            text-align: center;
        }

        .compare-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 6px rgba(43, 88, 118, 0.15);
        }

        .compare-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(43, 88, 118, 0.2);
            color: white;
        }

        .compare-action-icon {
            width: 20px;
            height: 20px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .compare-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding: 1rem;
            }

            .compare-actions {
                width: 100%;
            }

            .compare-clear-btn {
                width: 100%;
                justify-content: center;
            }

            .compare-product-card {
                min-width: 160px;
                padding: 1rem;
            }

            .product-image-container {
                height: 120px;
            }
        }

        @media (max-width: 480px) {
            .compare-products-scroller {
                padding: 1rem;
            }

            .compare-product-card {
                min-width: 140px;
            }

            .product-name {
                font-size: 0.85rem;
            }

            .product-price {
                font-size: 1rem;
            }
        }
    </style>
</div>
