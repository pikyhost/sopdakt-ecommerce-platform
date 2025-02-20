<div class="product-action">
    <!-- Wishlist Button -->
    <a wire:click="toggleLove" class="btn-icon-wish" title="Wishlist" style="cursor: pointer;">
        <i class="icon-heart" style="color: {{ $isLoved ? 'red' : 'gray' }};"></i>
    </a>

    <!-- View Product Details -->
    <span class="btn-icon btn-add-cart">
        <i class="fa fa-arrow-right"></i><span>SELECT OPTIONS</span>
    </span>

    <!-- Quick View -->
    <a wire:navigate href="{{ route('product.show', $product->slug) }}" class="btn-quickview" title="Quick View">
        <i class="fas fa-external-link-alt"></i>
    </a>
</div>
