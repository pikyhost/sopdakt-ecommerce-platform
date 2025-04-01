<div>
    @if(count($compareProducts) > 0)
        <div class="compare-section" style="margin-top: 20px; border: 1px solid #ddd; padding: 10px; border-radius: 8px;">
            <h4>Compare Products</h4>
            <div style="display: flex; gap: 10px; overflow-x: auto;">
                @foreach($compareProducts as $product)
                    <div style="border: 1px solid #ccc; padding: 10px; border-radius: 5px; text-align: center;">
                        <img src="{{ $product->getFirstMediaUrl('feature_product_image') }}" alt="{{ $product->name }}" style="width: 80px; height: 80px;">
                        <p>{{ $product->name }}</p>
                        <p>{{ $product->discount_price_for_current_country }}</p>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 10px; display: flex; gap: 10px;">
                <button wire:click="clearCompare" style="padding: 5px 10px; background: red; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Clear Compare
                </button>

                <a href="{{ route('compare.products', ['ids' => implode(',', $compareProducts->pluck('id')->toArray())]) }}"
                   style="padding: 5px 10px; background: blue; color: white; text-decoration: none; border-radius: 4px; cursor: pointer;">
                    Compare Now
                </a>
            </div>
        </div>
    @endif
</div>
