<div class="product-action">
    <!-- Wishlist Button (Toggle Love) -->
    <a wire:click="toggleLove" class="btn-icon-wish" title="Wishlist" style="cursor: pointer;">
        <i class="icon-heart {{ $isLoved ? 'text-danger' : 'text-secondary' }}"></i>
    </a>

    <!-- View Product Details -->
    <a href="{{ route('product.show', $product->slug) }}" class="btn-icon btn-add-cart">
        <i class="fa fa-arrow-right"></i><span>SELECT OPTIONS</span>
    </a>

    <!-- Quick View -->
    <a href="javascript:void(0);" wire:click="$emit('openQuickView', {{ $product->id }})" class="btn-quickview" title="Quick View">
        <i class="fas fa-external-link-alt"></i>
    </a>
</div>
