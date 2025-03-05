<div>
    <!-- Add to Cart Button -->
    <button wire:click="openModal" class="btn btn-primary">
        <i class="fas fa-shopping-cart me-2"></i> Add to Cart
    </button>

    <!-- Modal -->
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-box-open me-2"></i> {{ $product->name }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Color Selection -->
                        <div class="mb-3">
                            <label for="color" class="form-label fw-bold">Select Color</label>
                            <select wire:model.live="colorId" id="color" class="form-select">
                                <option value="">-- Choose Color --</option>
                                @foreach($colors as $color)
                                    <option value="{{ $color->id }}">{{ $color->name }}</option>
                                @endforeach
                            </select>
                            @error('colorId') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Size Selection -->
                        <div class="mb-3">
                            <label for="size" class="form-label fw-bold">Select Size</label>
                            <select wire:model.live="sizeId" id="size" class="form-select">
                                <option value="">-- Choose Size --</option>
                                @foreach($sizes as $size)
                                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                                @endforeach
                            </select>
                            @error('sizeId') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Quantity Input -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Quantity</label>
                            <input type="number" wire:model="quantity" min="1" class="form-control">
                            @error('quantity') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Price -->
                        <div class="text-center mt-3">
                            <h4 class="fw-bold text-success">
                                Price: ${{ number_format($product->price, 2) }}
                            </h4>
                        </div>

                        <!-- Cart Error Message -->
                        @if ($errors->has('cart_error'))
                            <div class="alert alert-danger mt-3">
                                {{ $errors->first('cart_error') }}
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer bg-light">
                        <a href="{{ route('product.show', $product->slug) }}" class="btn btn-link text-primary">
                            <i class="fas fa-info-circle me-1"></i> See All Item Details
                        </a>
                        <button wire:click="closeModal" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button wire:click="addToCart" class="btn btn-success">
                            <i class="fas fa-cart-plus me-1"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
