<div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <!-- Add to Cart Button -->
    <button wire:click="openModal" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm rounded-pill px-4 py-2"
            wire:loading.attr="disabled" wire:target="openModal">
        <span wire:loading wire:target="openModal">
            <i class="fa fa-spinner fa-spin"></i>
        </span>
        <i class="fas fa-shopping-cart"></i> {{ __('Add to Cart') }}
    </button>

    <!-- Modal -->
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4">

                    <!-- Modal Header -->
                    <div class="modal-header bg-gradient-primary text-white rounded-top">
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <i class="fas fa-box-open"></i> {{ $product->name }}
                        </h5>
                        <button type="button" class="btn-close text-white" wire:click="closeModal"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        @if($product->productColors->isNotEmpty())
                            <div class="row g-3">
                                <!-- Color Selection -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('Select Color') }}</label>
                                    <select wire:model.live="colorId" class="form-select shadow-sm rounded-pill text-{{ app()->getLocale() === 'ar' ? 'end' : 'start' }}">
                                        <option value="">{{ __('-- Choose Color --') }}</option>
                                        @foreach($colors as $color)
                                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('colorId') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <!-- Size Selection -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('Select Size') }}</label>
                                    <select wire:model.live="sizeId" class="form-select shadow-sm rounded-pill text-{{ app()->getLocale() === 'ar' ? 'end' : 'start' }}">
                                        <option value="">{{ __('-- Choose Size --') }}</option>
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sizeId') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        @endif

                        <!-- Quantity & Price -->
                        <div class="row g-3 mt-3">
                            <!-- Quantity Input -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Quantity') }}</label>
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
                                @error('quantity') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <!-- Price Display -->
                            <div class="col-md-6 d-flex align-items-center justify-content-center">
                                <h4 class="fw-bold text-success mb-0">{{ $product->discount_price_for_current_country }}</h4>
                            </div>
                        </div>

                        <!-- Cart Error Message -->
                        @if ($errors->has('cart_error'))
                            <div class="alert alert-danger mt-3">
                                {{ $errors->first('cart_error') }}
                            </div>
                        @endif
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer bg-light rounded-bottom d-flex justify-content-{{ app()->getLocale() === 'ar' ? 'start' : 'between' }}">
                        <button wire:click="closeModal" class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="fas fa-times"></i> {{ __('Cancel') }}
                        </button>
                        <button wire:click="addToCart" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm rounded-pill px-4 py-2"
                                wire:loading.attr="disabled" wire:target="addToCart">
                            <span wire:loading wire:target="addToCart">
                                <i class="fa fa-spinner fa-spin"></i>
                            </span>
                            <i class="fas fa-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>
