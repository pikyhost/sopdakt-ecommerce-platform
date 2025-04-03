<div>
    <div class="comparison-wrapper">
        <div class="container py-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Header with black background -->
                <div class="card-header bg-black text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="h3 mb-0 fw-bold text-white">
                            Product Comparison
                        </h2>
                        <span class="badge bg-white text-black rounded-pill px-3 py-2">
                            {{ count($products) }} Items
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive-lg">
                        <table class="table table-compare align-middle mb-0">
                            <thead class="bg-light">
                            <tr>
                                <th class="p-4 fw-bold text-start" style="width: 22%;">
                                    <span class="d-block mb-1">Key Features</span>
                                    <small class="text-muted fw-normal">Compare specifications</small>
                                </th>
                                @foreach($products as $product)
                                    <th class="p-4 text-center position-relative compare-product-col">
                                        <div class="compare-product-header">
                                            <h1 class="d-block fw-bold fs-5 text-dark mb-2">{{ $product->name }}</h1>
                                            <span class="d-block text-muted small mb-3">{{ $product->category->name ?? '' }}</span>
                                            <div class="ratings-container">
                                                <div class="product-ratings">
                                                    <span class="ratings" style="width: {{ $product->fake_average_rating * 20 }}%"></span>
                                                    <span class="tooltiptext tooltip-top"></span>
                                                </div>
                                                <span>( {{ $product->ratings()->count() }} Reviews )</span>
                                            </div>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Product Images -->
                            <tr class="compare-images-row">
                                <td class="p-4 fw-semibold text-muted bg-light">Visual Comparison</td>
                                @foreach($products as $product)
                                    <td class="p-4">
                                        <div class="compare-product-image">
                                            <div class="image-wrapper">
                                                <img src="{{ $product->getFirstMediaUrl('feature_product_image') }}"
                                                     class="img-fluid rounded-3 shadow-sm border p-2 bg-white"
                                                     alt="{{ $product->name }}"
                                                     loading="lazy">
                                                @if($product->discount_percent)
                                                    <span class="discount-badge bg-danger text-white">
                                                        -{{ $product->discount_percent }}%
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Price Comparison -->
                            <tr class="compare-price-row">
                                <td class="p-3 fw-semibold text-muted">Pricing</td>
                                @foreach($products as $product)
                                    <td class="p-3">
                                        <div class="price-display">
                                            <span class="current-price text-success fw-bold">
                                                {{ $product->discount_price_for_current_country }}
                                            </span>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Color Options -->
                            <tr>
                                <td class="p-3 fw-semibold text-muted">Color Options</td>
                                @foreach($products as $product)
                                    <td class="p-3">
                                        <div class="color-options">
                                            @forelse($product->productColors as $productColor)
                                                <div class="color-option">
                                                    <input type="radio"
                                                           name="color-{{ $product->id }}"
                                                           id="color-{{ $product->id }}-{{ $productColor->id }}"
                                                        {{ $productColor->is_default ? 'checked' : '' }}>
                                                    <label for="color-{{ $product->id }}-{{ $productColor->id }}"
                                                           class="color-swatch"
                                                           style="background-color: {{ $productColor->color->code ?? '#000' }};"
                                                           data-bs-toggle="tooltip"
                                                           title="{{ $productColor->color->name }}">
                                                        @if($productColor->is_default)
                                                            <span class="selected-indicator">
                                                                <i class="fas fa-check"></i>
                                                            </span>
                                                        @endif
                                                    </label>
                                                </div>
                                            @empty
                                                <span class="text-muted small">No color options</span>
                                            @endforelse
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Size Options -->
                            <tr class="bg-light">
                                <td class="p-3 fw-semibold text-muted">Size Options</td>
                                @foreach($products as $product)
                                    <td class="p-3">
                                        <div class="size-options">
                                            @forelse($product->productColors as $productColor)
                                                <div class="size-group mb-2">
                                                    @if($product->productColors->count() > 1)
                                                        <div class="size-group-label mb-1">
                                                            <small class="text-muted">{{ $productColor->color->name ?? 'All Colors' }}</small>
                                                        </div>
                                                    @endif
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach($productColor->sizes as $size)
                                                            <div class="size-option">
                                                                <input type="radio"
                                                                       name="size-{{ $product->id }}"
                                                                       id="size-{{ $product->id }}-{{ $size->id }}">
                                                                <label for="size-{{ $product->id }}-{{ $size->id }}"
                                                                       class="size-label">
                                                                    {{ $size->name }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @empty
                                                <span class="text-muted small">No size options</span>
                                            @endforelse
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Availability & Shipping -->
                            <tr>
                                <td class="p-3 fw-semibold text-muted">Availability</td>
                                @foreach($products as $product)
                                    <td class="p-3">
                                        <div class="availability-info">
                                            <span class="badge bg-{{ $product->quantity > 0 ? 'success' : 'danger' }}-subtle text-{{ $product->quantity > 0 ? 'success' : 'danger' }} rounded-pill d-inline-flex align-items-center mb-2">
                                                <i class="fas fa-{{ $product->quantity > 0 ? 'check-circle' : 'times-circle' }} me-1"></i>
                                                {{ $product->quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                                            </span>
                                            @if($product->is_free_shipping)
                                                <div class="shipping-info">
                                                    <i class="fas fa-shipping-fast text-muted me-2"></i>
                                                    <small class="text-muted">Free shipping on orders</small>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Decision Row -->
                            <tr class="decision-row bg-light">
                                <td class="p-3 fw-semibold text-muted">Your Choice</td>
                                @foreach($products as $product)
                                    <td class="p-3">
                                        <livewire:product-actions :product="$product" wire:key="product-{{ $product->id }}" />
                                    </td>
                                @endforeach
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer with summary -->
                <div class="card-footer bg-light py-4 hover:bg-black transition-colors duration-300">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <a href="{{ url('/') }}" class="btn btn-outline-dark rounded-pill px-4">
                                <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .comparison-wrapper {
            --black: #000000;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --border-radius: 0.5rem;
        }

        .table-compare {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .table-compare th, .table-compare td {
            border-right: 1px solid var(--medium-gray);
            border-bottom: 1px solid var(--medium-gray);
            vertical-align: middle;
        }

        .table-compare th:last-child, .table-compare td:last-child {
            border-right: none;
        }

        .compare-product-col {
            background-color: var(--white);
            transition: all 0.3s ease;
        }

        .compare-product-col:hover {
            box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.05);
        }

        .compare-product-header {
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .compare-images-row td {
            padding-top: 2rem !important;
            padding-bottom: 2rem !important;
        }

        .image-wrapper {
            position: relative;
            max-width: 180px;
            margin: 0 auto;
        }

        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.75rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .price-display {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .current-price {
            font-size: 1.5rem;
            line-height: 1;
        }

        .color-options {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .color-option {
            position: relative;
        }

        .color-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .color-swatch {
            display: inline-block;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
            position: relative;
        }

        .color-option input[type="radio"]:checked + .color-swatch {
            border-color: var(--black);
            transform: scale(1.1);
        }

        .selected-indicator {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 0.6rem;
            text-shadow: 0 0 2px rgba(0,0,0,0.5);
        }

        .size-options {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .size-option {
            position: relative;
        }

        .size-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .size-label {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border: 1px solid var(--medium-gray);
            border-radius: 50px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .size-option input[type="radio"]:checked + .size-label {
            background-color: var(--black);
            color: var(--white);
            border-color: var(--black);
        }

        .decision-row .btn {
            transition: all 0.2s ease;
        }

        .decision-row .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Hover effects */
        .card-footer:hover {
            background-color: var(--black) !important;
            color: var(--white) !important;
        }

        .card-footer:hover .btn-outline-dark {
            color: var(--white) !important;
            border-color: var(--white) !important;
        }

        @media (max-width: 992px) {
            .table-responsive-lg {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-compare {
                min-width: 992px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Highlight column on hover
            const productCols = document.querySelectorAll('.compare-product-col');
            productCols.forEach(col => {
                col.addEventListener('mouseenter', function() {
                    const index = Array.from(this.parentNode.children).indexOf(this);
                    document.querySelectorAll('tbody tr').forEach(row => {
                        if (row.children[index]) {
                            row.children[index].classList.add('highlight-column');
                        }
                    });
                });

                col.addEventListener('mouseleave', function() {
                    const index = Array.from(this.parentNode.children).indexOf(this);
                    document.querySelectorAll('tbody tr').forEach(row => {
                        if (row.children[index]) {
                            row.children[index].classList.remove('highlight-column');
                        }
                    });
                });
            });
        });
    </script>
</div>
