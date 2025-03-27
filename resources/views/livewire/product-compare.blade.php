<div>
    @if($compareProducts->isNotEmpty())
        <div class="compare-section" style="margin-top: 20px; border: 1px solid #ddd; padding: 10px; border-radius: 8px;">
            <h4>Compare Products</h4>
            <div style="display: flex; gap: 10px; overflow-x: auto;">
                @foreach($compareProducts as $product)
                    <div style="border: 1px solid #ccc; padding: 10px; border-radius: 5px; text-align: center;">
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" style="width: 80px; height: 80px;">
                        <p>{{ $product->name }}</p>
                        <p>${{ $product->price }}</p>
                    </div>
                @endforeach
            </div>
            <button wire:click="clearCompare" style="margin-top: 10px; padding: 5px 10px; background: blue; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Clear Compare
            </button>
        </div>
    @endif
</div>
