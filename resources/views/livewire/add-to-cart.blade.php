<div class="container py-4">
    <div class="card shadow-lg p-4">
        <!-- Color & Size Selection -->
        @if(\App\Models\Product::find($productId)->productColors->isNotEmpty())
            <div class="row g-4 mb-4">
                <!-- Color Selection -->
                <div class="col-md-6">
                    <label for="color" class="form-label fw-bold">Select Color</label>
                    <select wire:model.live="colorId" id="color" class="form-select form-select-lg">
                        <option value="">-- Choose Color --</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                        @endforeach
                    </select>
                    @error('colorId') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <!-- Size Selection -->
                @if(!empty($sizes))
                    <div class="col-md-6">
                        <label for="size" class="form-label fw-bold">Select Size</label>
                        <select wire:model.live="sizeId" id="size" class="form-select form-select-lg">
                            <option value="">-- Choose Size --</option>
                            @foreach($sizes as $size)
                                <option value="{{ $size->id }}">{{ $size->name }}</option>
                            @endforeach
                        </select>
                        @error('sizeId') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                @endif
            </div>
        @endif

        <!-- Product Actions -->
        <div class="d-flex flex-column flex-md-row align-items-center gap-4">
            <!-- Quantity Input -->
            <div class="d-flex align-items-center gap-3">
                <label class="fw-bold mb-0">Quantity:</label>
                <div class="d-flex align-items-center border rounded-3 overflow-hidden">
                    <button class="btn btn-outline-danger px-3" type="button" wire:click="decreaseQuantity">
                        <i class="fa fa-minus"></i>
                    </button>
                    <span class="px-4 py-2 bg-light text-dark fw-bold" style="min-width: 50px; text-align: center;">
                        {{ $quantity }}
                    </span>
                    <button class="btn btn-outline-success px-3" type="button" wire:click="increaseQuantity">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
            @error('quantity') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <!-- Cart Actions -->
        <div class="d-flex flex-column flex-md-row align-items-center gap-3 mt-4">
            <button wire:click="addToCart" class="btn btn-primary btn-lg d-flex align-items-center px-4 py-3" wire:loading.attr="disabled" wire:target="addToCart">
                <i class="fa fa-shopping-cart me-2"></i> Add to Cart ({{ $cartTotalQuantity }})
                <span wire:loading wire:target="addToCart" class="ms-2">
                    <i class="fa fa-spinner fa-spin"></i>
                </span>
            </button>
            <a href="{{ route('cart.index') }}" class="btn btn-dark btn-lg px-4 py-3">
                <i class="fa fa-shopping-bag me-2"></i> View Cart
            </a>
        </div>

        <!-- Messages -->
        @if ($errors->has('cart_error'))
            <div class="alert alert-danger mt-3">
                {{ $errors->first('cart_error') }}
            </div>
        @endif
        @if (session()->has('success'))
            <div class="alert alert-success mt-3 fade show" role="alert">
                {{ session('success') }}
            </div>
        @endif
    </div>
</div>
