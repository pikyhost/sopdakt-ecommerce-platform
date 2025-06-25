<div>
    <div class="container wishlist-container">
        <h1>
            {{ __('My wishlist on Porto Shop') }}
            <span id="wishlist-count">{{ count($wishlist) }}</span>
        </h1>

        <table>
            <thead>
            <tr>
                <th class="none"></th>
                <th>{{ __('PRODUCT') }}</th>
                <th>{{ __('PRICE') }}</th>
                <th>{{ __('STOCK STATUS') }}</th>
                <th>{{ __('ACTIONS') }}</th>
            </tr>
            </thead>
            <tbody id="wishlist-body">
            @forelse($wishlist as $product)
                <tr class="wishlist-item">
                    <td data-label="">
                        <img src="{{ $product->getFirstMediaUrl('feature_product_image') }}"
                             alt="{{ $product->name }}"
                             class="product-image">
                        <button wire:click.prevent="removeFromWishlist({{ $product->id }})"
                                class="delete-from-fav"
                                title="{{ __('Remove Product') }}">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </td>
                    <td data-label="PRODUCT">
                        <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                    </td>
                    <td data-label="PRICE">{{ $product->discount_price_for_current_country }}</td>
                    <td data-label="STOCK STATUS" style="color: {{ $product->stock_status === 'available' ? 'green' : 'red' }};">
                        {{ $product->stock_status === 'available' ? __('In stock') : __('Out of stock') }}
                    </td>
                    <td data-label="ACTIONS">
                        @if($product->quantity > 0)
                            @livewire('add-to-cart-wishlist', ['product' => $product], key($product->id))
                        @endif
                        <button class="view"
                                wire:click="quickView({{ $product->id }})"
                                onclick="event.stopPropagation();">
                            {{ __('QUICK VIEW') }}
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">
                        <p>{{ __('Your wishlist is empty.') }}</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
