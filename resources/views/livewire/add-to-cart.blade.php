<div>
    <!-- Color Selection (Comes First) -->
    @if(\App\Models\Product::find($productId)->productColors->isNotEmpty())
    <div class="mb-3">
        <label for="color" class="block text-sm font-medium text-gray-700">Select Color</label>
        <select wire:model.live="colorId" id="color" class="form-control border-gray-300 rounded-md shadow-sm">
            <option value="">-- Choose Color --</option>
            @foreach($colors as $color)
                <option value="{{ $color->id }}">{{ $color->name }}</option>
            @endforeach
        </select>
        @error('colorId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <!-- Size Selection (Shows Only After Color is Selected) -->
    @if(!empty($sizes))
        <div class="mb-3">
            <label for="size" class="block text-sm font-medium text-gray-700">Select Size</label>
            <select wire:model.live="sizeId" id="size" class="form-control border-gray-300 rounded-md shadow-sm">
                <option value="">-- Choose Size --</option>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                @endforeach
            </select>
            @error('sizeId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    @endif

    @endif

    <!-- Product Action -->
    <div class="product-action flex items-center space-x-4">
        <!-- Quantity Input -->
        <div class="product-single-qty">
            <input type="number" min="1" step="1" wire:model="quantity"
                   class="horizontal-quantity form-control w-20 text-center border-gray-300 rounded-md shadow-sm">
        </div>

        <!-- Cart Error Message -->
        @if ($errors->has('cart_error'))
            <div class="alert alert-danger mt-3">
                {{ $errors->first('cart_error') }}
            </div>
        @endif

        <!-- Add to Cart Button -->
        <button wire:click="addToCart"
                class="btn btn-dark add-cart flex items-center px-4 py-2 rounded-lg text-white bg-blue-600 hover:bg-blue-700"
                wire:loading.attr="disabled">
            Add to Cart ({{ $cartTotalQuantity }})
        </button>

        <!-- View Cart -->
        <a href="{{ route('cart.index') }}"
           class="btn btn-gray view-cart px-4 py-2 rounded-lg bg-gray-500 text-white hover:bg-gray-600">
            View Cart
        </a>
    </div>

    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-3 mt-3 rounded-md shadow-md"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)">
            {{ session('success') }}
        </div>
    @endif
</div>
