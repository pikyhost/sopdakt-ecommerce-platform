<div>
    <a href="javascript:void(0);" title="Cart" class="dropdown-toggle dropdown- cart-toggle">
        <i class="minicart-icon"></i>
        <span class="cart-count badge-circle">{{ $cartCount }}</span>
    </a>

    <div class="cart-overlay"></div>

    <div class="dropdown-menu mobile-cart">
        <span title="Close (Esc)" class="btn-close">×</span>

        <div class="dropdownmenu-wrapper custom-scrollbar">
            <div class="dropdown-cart-header">Shopping Cart</div>

            <div class="dropdown-cart-products">
                @forelse ($cartItems as $item)
                    <div class="product">
                        <div class="product-details">
                            <h4 class="product-title">
                                <a href="">
                                    {{ $item['product']['name'] ?? 'N/A' }}
                                </a>
                            </h4>

                            <span class="cart-product-info">
                                <span class="cart-product-qty">{{ $item['quantity'] }}</span>
                                × {{ $item['product']['price'] ?? '' }}
                            </span>
                        </div><!-- End .product-details -->

                        <figure class="product-image-container">
                            <a href="{{ route('product.show', $item['product']['slug'] ?? '#') }}" class="product-image">
                                <img src="{{ $item['product']['feature_product_image_url'] ?? '' }}"
                                     alt="{{ $item['product']['name'] ?? 'Product Image' }}"
                                     width="80" height="80">
                            </a>

                            <a href="javascript:void(0);" wire:click="removeFromCart({{ $item['id'] }})"
                               class="btn-remove" title="Remove Product"><span>×</span></a>
                        </figure>
                    </div><!-- End .product -->
                @empty
                    <p class="text-center p-3">Your cart is empty.</p>
                @endforelse
            </div><!-- End .cart-product -->

            @if($cartCount > 0)

                <div class="dropdown-cart-total">
                    <span>SUBTOTAL:</span>
                    <span class="cart-total-price float-right">{{ $subtotal }}</span>
                </div><!-- End .dropdown-cart-total -->

                <div class="dropdown-cart-action">
                    <a href="{{ route('cart.index') }}" class="btn btn-gray btn-block view-cart">View Cart</a>
                    <a href="{{ route('checkout.index') }}" class="btn btn-dark btn-block">Checkout</a>

                </div><!-- End .dropdown-cart-total -->
            @endif
        </div><!-- End .dropdownmenu-wrapper -->
    </div><!-- End .dropdown-menu -->
</div>
