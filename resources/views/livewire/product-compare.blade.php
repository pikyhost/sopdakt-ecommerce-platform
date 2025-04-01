<div>
    <div class="compare-products-ultra">
        @if(count($compareProducts) > 0)
            <div class="compare-ultra-container">
                <!-- Premium Header -->
                <div class="compare-ultra-header">
                    <div class="compare-ultra-title-group">
                        <svg class="compare-ultra-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 3H19C20.1046 3 21 3.89543 21 5V11M3 13V19C3 20.1046 3.89543 21 5 21H11M16 8L12 12M12 12L8 16M12 12L16 16M12 12L8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div>
                            <h3 class="compare-ultra-title">Product Comparison</h3>
                            <p class="compare-ultra-subtitle">{{ count($compareProducts) }} items selected</p>
                        </div>
                    </div>
                    <button wire:click="clearCompare" class="compare-ultra-clear">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M6 6L18 18M6 18L18 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        Clear All
                    </button>
                </div>

                <!-- Luxury Products Grid -->
                <div class="compare-ultra-scroller">
                    <div class="compare-ultra-grid">
                        @foreach($compareProducts as $product)
                            <div class="compare-ultra-card">
                                <button wire:click="removeFromCompare({{ $product->id }})"
                                        class="compare-ultra-remove"
                                        aria-label="Remove {{ $product->name }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <path d="M6 6L18 18M6 18L18 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </button>

                                <div class="ultra-image-container">
                                    <img src="{{ $product->getFirstMediaUrl('feature_product_image') }}"
                                         alt="{{ $product->name }}"
                                         loading="lazy"
                                         width="160"
                                         height="160"
                                         class="ultra-product-image">
                                    <div class="ultra-image-overlay"></div>
                                </div>

                                <div class="ultra-product-details">
                                    <h4 class="ultra-product-name">{{ $product->name }}</h4>

                                    <div class="ultra-price-container">
                                    <span class="ultra-price">
                                        @php
                                            $price = $product->after_discount_price ?? $product->price;
                                            echo number_format($price, 2);
                                        @endphp
                                    </span>
                                        <span class="ultra-currency">
                                        {{ \App\Models\Setting::getCurrency()?->code ?? '' }}
                                    </span>
                                    </div>

                                    <div class="ultra-product-meta">
                                    <span class="ultra-rating">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                                        </svg>
                                        4.8
                                    </span>
                                        <span class="ultra-availability">In Stock</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Premium Action Footer -->
                @if(count($compareProducts) > 1)
                    <div class="compare-ultra-footer">
                        <a href="{{ route('compare.products', ['ids' => implode(',', $compareProducts->pluck('id')->toArray())]) }}"
                           class="ultra-compare-btn">
                            <span>Compare Products</span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M5 12H19M19 12L15 16M19 12L15 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <style>
        /* Ultra Premium Color Scheme */
        :root {
            --ultra-primary: #1a365d;
            --ultra-secondary: #4a5568;
            --ultra-accent: #3182ce;
            --ultra-light: #f7fafc;
            --ultra-dark: #1a202c;
            --ultra-success: #38a169;
            --ultra-border: #e2e8f0;
            --ultra-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --ultra-transition: all 0.3s cubic-bezier(0.215, 0.61, 0.355, 1);
        }

        /* Base Container */
        .compare-products-ultra {
            margin: 3rem 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Main Container */
        .compare-ultra-container {
            background: white;
            border-radius: 16px;
            box-shadow: var(--ultra-shadow);
            overflow: hidden;
            border: 1px solid var(--ultra-border);
        }

        /* Premium Header */
        .compare-ultra-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, var(--ultra-primary), var(--ultra-secondary));
            color: white;
        }

        .compare-ultra-title-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .compare-ultra-icon {
            width: 28px;
            height: 28px;
            color: rgba(255, 255, 255, 0.9);
        }

        .compare-ultra-title {
            margin: 0;
            font-size: 1.375rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .compare-ultra-subtitle {
            margin: 0.25rem 0 0;
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 400;
        }

        /* Clear Button */
        .compare-ultra-clear {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--ultra-transition);
        }

        .compare-ultra-clear:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Products Scroller */
        .compare-ultra-scroller {
            overflow-x: auto;
            padding: 2rem;
            scrollbar-width: thin;
            scrollbar-color: var(--ultra-accent) var(--ultra-light);
        }

        .compare-ultra-scroller::-webkit-scrollbar {
            height: 6px;
        }

        .compare-ultra-scroller::-webkit-scrollbar-track {
            background: var(--ultra-light);
            border-radius: 3px;
        }

        .compare-ultra-scroller::-webkit-scrollbar-thumb {
            background: var(--ultra-accent);
            border-radius: 3px;
        }

        /* Luxury Products Grid */
        .compare-ultra-grid {
            display: flex;
            gap: 1.5rem;
            padding-bottom: 1rem;
        }

        /* Premium Product Card */
        .compare-ultra-card {
            position: relative;
            min-width: 220px;
            max-width: 240px;
            border-radius: 12px;
            overflow: hidden;
            transition: var(--ultra-transition);
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--ultra-border);
        }

        .compare-ultra-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Remove Button */
        .compare-ultra-remove {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: white;
            color: var(--ultra-primary);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--ultra-transition);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .compare-ultra-remove:hover {
            background: #f56565;
            color: white;
            transform: rotate(90deg);
        }

        /* Product Image */
        .ultra-image-container {
            position: relative;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--ultra-light);
        }

        .ultra-product-image {
            max-height: 80%;
            max-width: 80%;
            object-fit: contain;
            transition: var(--ultra-transition);
        }

        .ultra-image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(255,255,255,0.3), rgba(255,255,255,0));
            z-index: 1;
        }

        /* Product Details */
        .ultra-product-details {
            padding: 1.5rem;
        }

        .ultra-product-name {
            font-size: 1rem;
            margin: 0 0 0.75rem;
            color: var(--ultra-dark);
            font-weight: 600;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.8em;
        }

        /* Price Container */
        .ultra-price-container {
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
            margin-bottom: 1rem;
        }

        .ultra-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--ultra-primary);
        }

        .ultra-currency {
            font-size: 0.875rem;
            color: var(--ultra-secondary);
            opacity: 0.8;
        }

        /* Product Meta */
        .ultra-product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ultra-rating {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.8125rem;
            color: var(--ultra-secondary);
        }

        .ultra-rating svg {
            color: #f6ad55;
        }

        .ultra-availability {
            font-size: 0.8125rem;
            color: var(--ultra-success);
            font-weight: 500;
        }

        /* Premium Footer */
        .compare-ultra-footer {
            padding: 1.5rem 2rem;
            border-top: 1px solid var(--ultra-border);
            text-align: center;
        }

        .ultra-compare-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            background: linear-gradient(135deg, var(--ultra-primary), var(--ultra-accent));
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--ultra-transition);
            box-shadow: 0 4px 6px rgba(49, 130, 206, 0.2);
            width: 100%;
            max-width: 300px;
        }

        .ultra-compare-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(49, 130, 206, 0.3);
        }

        .ultra-compare-btn svg {
            transition: transform 0.3s ease;
        }

        .ultra-compare-btn:hover svg {
            transform: translateX(3px);
        }

        /* Responsive Excellence */
        @media (max-width: 1024px) {
            .compare-ultra-card {
                min-width: 200px;
            }
        }

        @media (max-width: 768px) {
            .compare-ultra-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding: 1.25rem;
            }

            .compare-ultra-scroller {
                padding: 1.25rem;
            }

            .ultra-product-details {
                padding: 1.25rem;
            }
        }

        @media (max-width: 480px) {
            .compare-ultra-card {
                min-width: 180px;
            }

            .ultra-image-container {
                height: 150px;
            }

            .ultra-compare-btn {
                padding: 0.875rem 1.5rem;
                font-size: 0.9375rem;
            }
        }
    </style>
</div>
