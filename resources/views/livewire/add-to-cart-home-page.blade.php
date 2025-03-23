<div>
    <div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <a href="#"
           class="btn-icon btn-add-cart product-type-simple"
           style="cursor: pointer;"
           onclick="event.stopPropagation();"
           wire:click.prevent="openModal"
           wire:target="openModal">
            <i class="icon-shopping-cart"></i>
        </a>

        @if($showModal)
            <div class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>{{ $product->name }}</h5>
                        <button class="btn btn-secondary" wire:click="closeModal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @if($product->productColors->isNotEmpty())
                            <div class="selection-container">
                                <div>
                                    <label>Select Color:</label>
                                    <select wire:model.live="colorId">
                                        <option value="">Choose Color</option>
                                        @foreach($colors as $color)
                                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('colorId') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div>
                                    <label>Select Size:</label>
                                    <select wire:model.live="sizeId">
                                        <option value="">Choose Size</option>
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sizeId') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        @endif

                        <div class="quantity-container">
                            <button class="quantity-btn" wire:click="decreaseQuantity">-</button>
                            <span class="quantity-display">{{ $quantity }}</span>
                            <button class="quantity-btn" wire:click="increaseQuantity">+</button>
                        </div>

                        <h4>{{ $product->discount_price_for_current_country }}</h4>
                    </div>

                    @if ($errors->has('cart_error'))
                        <div class="alert alert-danger mt-3">
                            {{ $errors->first('cart_error') }}
                        </div>
                    @endif

                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                        <button class="btn btn-dark" wire:click="addToCart" wire:loading.attr="disabled">
                        <span wire:loading wire:target="addToCart">
                            <i class="fa fa-spinner fa-spin"></i>
                        </span>
                            {{ __('Add to Cart') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            z-index: 9999;
        }

        .modal-content {
            background: #fff;
            border-radius: 10px;
            width: 50%;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .modal-header, .modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .modal-footer {
            border-top: 1px solid #ddd;
        }

        .modal-body {
            padding: 20px;
        }

        .btn {
            cursor: pointer;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn-primary {
            background: #007bff;
            color: #fff;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #ddd;
            color: #333;
        }

        .btn-secondary:hover {
            background: #ccc;
        }

        .quantity-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-btn {
            background: #f0f0f0;
            padding: 8px 12px;
            cursor: pointer;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin: 0 5px;
        }

        .quantity-display {
            padding: 8px 16px;
            background: #e0e0e0;
            border-radius: 5px;
        }

        .selection-container {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .selection-container div {
            flex: 1;
        }
    </style>

</div>
