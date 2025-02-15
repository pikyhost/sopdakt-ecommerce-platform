<div>
    <button wire:click="toggleWishlist" class="btn-icon-wish wishlist-btn">
        @if($isWishlisted)
            <i class="icon-wishlist-filled"></i>
            <span class="wishlist-text">Remove from Wishlist</span>
        @else
            <i class="icon-wishlist-2"></i>
            <span class="wishlist-text">Add to Wishlist</span>
        @endif
    </button>
</div>
