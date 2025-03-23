<div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="container">
        <div class="wishlist-title">
            <h2 class="p-2">{{ __('My Wishlist') }}</h2>
        </div>
        <div class="wishlist-table-container">
            <table class="table table-wishlist mb-0">
                <thead>
                <tr>
                    <th class="thumbnail-col"></th>
                    <th class="product-col">{{ __('Product') }}</th>
                    <th class="price-col">{{ __('Price') }}</th>
                    <th class="status-col">{{ __('Stock Status') }}</th>
                    <th class="action-col">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($wishlist as $product)
                    <tr class="product-row">
                        <td>
                            <figure class="product-image-container">
                                <a href="{{ route('product.show', $product->slug) }}" class="product-image">
                                    <img src="{{ $product->getFirstMediaUrl('feature_product_image') }}"
                                         alt="{{ $product->name }}">
                                </a>
                                <a href="#" wire:click.prevent="removeFromWishlist({{ $product->id }})"
                                   class="btn-remove icon-cancel" title="{{ __('Remove Product') }}"></a>
                            </figure>
                        </td>
                        <td>
                            <h5 class="product-title">
                                <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                            </h5>
                        </td>
                        <td class="price-box">{{ $product->discount_price_for_current_country }}</td>
                        <td>
                            <span class="stock-status {{ $product->quantity > 1 ? 'text-success' : 'text-danger' }}">
                                {{ $product->quantity > 1 ? __('In stock') : __('Out of stock') }}
                            </span>
                        </td>
                        <td class="action">
                            <div class="d-flex align-items-center gap-2">
                                <!-- Quick View Button -->
                                <a
                                    href="{{ route('product.show', $product->slug) }}"
                                    title="{{ __('Quick View') }}"
                                    onclick="event.stopPropagation();"
                                    wire:click="quickView({{ $product->id }})" class="btn btn-quickview">
                                    {{ __('Quick View') }}
                                </a>

                                <!-- Add to Cart Button -->
                                @if($product->quantity > 0)
                                    @livewire('add-to-cart-wishlist', ['product' => $product])
                                @endif
                            </div>
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
</div>
