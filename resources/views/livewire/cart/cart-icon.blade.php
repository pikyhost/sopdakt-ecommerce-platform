<div>
    <a href="javascript:void(0);" title="Cart" class="dropdown-toggle dropdown-arrow cart-toggle">
        <i class="minicart-icon"></i>
        <span class="cart-count badge-circle">{{ $cartCount }}</span>
    </a>

    <div class="cart-overlay"></div>

    <div class="dropdown-menu mobile-cart">
        <a href="#" title="Close (Esc)" class="btn-close">×</a>

        <div class="dropdownmenu-wrapper custom-scrollbar">
            <div class="dropdown-cart-header">Shopping Cart</div>

            <div class="dropdown-cart-products">
                @forelse ($cartItems as $item)
                    <div class="product">
                        <div class="product-details">
                            <h4 class="product-title">
                                <a href="{{ route('product.show', $item->product->id) }}">
                                    {{ $item->product->name }}
                                </a>
                            </h4>

                            <span class="cart-product-info">
                                <span class="cart-product-qty">{{ $item->quantity }}</span>
                                × ${{ number_format($item->price_per_unit, 2) }}
                            </span>
                        </div><!-- End .product-details -->

                        <figure class="product-image-container">
                            <a href="{{ route('product.show', $item->product->id) }}" class="product-image">
                                <img src="{{ $item->product->getFeatureProductImageUrl() }}" alt="{{ $item->product->name }}"
                                     width="80" height="80">
                            </a>

                            <a href="javascript:void(0);" wire:click="removeFromCart({{ $item->id }})"
                               class="btn-remove" title="Remove Product"><span>×</span></a>
                        </figure>
                    </div><!-- End .product -->
                @empty
                    <p class="text-center p-3">Your cart is empty.</p>
                @endforelse
            </div><!-- End .cart-product -->

            <div class="dropdown-cart-total">
                <span>SUBTOTAL:</span>
                <span class="cart-total-price float-right">${{ number_format($subtotal, 2) }}</span>
            </div><!-- End .dropdown-cart-total -->

            <div class="dropdown-cart-action">
                <a href="{{ route('cart.index') }}" class="btn btn-gray btn-block view-cart">View Cart</a>
            </div><!-- End .dropdown-cart-total -->
        </div><!-- End .dropdownmenu-wrapper -->
    </div><!-- End .dropdown-menu -->
</div>
