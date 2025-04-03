<div>
    @if($products->count() > 0)
        <h2 class="mb-3">Products ({{ $products->count() }})</h2>
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-4 mb-4">
                    <!-- Your product card component here -->
                    @include('components.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    @endif

    @if($categories->count() > 0)
        <h2 class="mb-3 mt-5">Categories ({{ $categories->count() }})</h2>
        <div class="row">
            @foreach($categories as $category)
                <div class="col-md-4 mb-4">
                    <!-- Your category card component here -->
                    @include('components.category-card', ['category' => $category])
                </div>
            @endforeach
        </div>
    @endif

    @if($products->count() == 0 && $categories->count() == 0)
        <div class="alert alert-info">
            No results found for "{{ $query }}"
        </div>
    @endif
</div>
