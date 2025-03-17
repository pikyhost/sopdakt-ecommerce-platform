<div class="product-action" style="display: flex; gap: 6px; align-items: center;">
    <!-- Wishlist Button -->
    <a wire:click="toggleLove" title="Wishlist" style="
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border: 1px solid #ddd;
        border-radius: 50%;
        background-color: #fff;
        text-decoration: none;
        transition: all 0.3s ease-in-out;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    "
       onmouseover="this.style.backgroundColor='#f0f0f0'; this.style.boxShadow='0 2px 5px rgba(0, 0, 0, 0.2)';"
       onmouseout="this.style.backgroundColor='#fff'; this.style.boxShadow='0 1px 3px rgba(0, 0, 0, 0.1)';">
        <i class="fas fa-heart" style="color: {{ $isLoved ? 'red' : '#aaa' }}; font-size: 14px;"></i>
    </a>

    <!-- Add to Cart Component -->
    @livewire('add-to-cart-action', ['product' => $product])

    <!-- Quick View -->
    <a href="{{ route('product.show', $product->slug) }}" title="Quick View" style="
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border: 1px solid #ddd;
        border-radius: 50%;
        background-color: #fff;
        text-decoration: none;
        transition: all 0.3s ease-in-out;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    "
       onmouseover="this.style.backgroundColor='#f0f0f0'; this.style.boxShadow='0 2px 5px rgba(0, 0, 0, 0.2)';"
       onmouseout="this.style.backgroundColor='#fff'; this.style.boxShadow='0 1px 3px rgba(0, 0, 0, 0.1)';">
        <i class="fas fa-external-link-alt" style="color: #333; font-size: 14px;"></i>
    </a>
</div>
