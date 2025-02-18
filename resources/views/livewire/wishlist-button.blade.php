<div>
    <a href="javascript:void(0);" wire:click="toggleWishlist"
       class="btn-icon-wish add-wishlist justify-content-start wishlist-btn" title="{{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
        @if($isWishlisted)
            <i class="icon-wishlist-filled"></i>
            <span class="wishlist-text">Remove from Wishlist</span>
        @else
            <i class="icon-wishlist-2"></i>
            <span class="wishlist-text">Add to Wishlist</span>
        @endif
    </a>
</div>
