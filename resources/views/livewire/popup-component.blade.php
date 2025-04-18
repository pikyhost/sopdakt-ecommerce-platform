<div>
    <style>
        .newsletter-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: row;
            width: 90%;
            max-width: 700px;
            z-index: 9999;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .newsletter-content {
            flex: 1;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .newsletter-content h2 {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .newsletter-content p {
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .newsletter-form {
            display: flex;
            background: #f1f1f1;
            border-radius: 50px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .newsletter-form input[type="email"] {
            flex: 1;
            padding: 12px 20px;
            border: none;
            outline: none;
            background: transparent;
        }

        .newsletter-form button {
            padding: 12px 25px;
            border: none;
            background: #000;
            color: #fff;
            font-weight: bold;
            border-radius: 0 50px 50px 0;
            cursor: pointer;
        }

        .newsletter-checkbox {
            font-size: 13px;
        }

        .newsletter-popup-image {
            flex: 1;
            background-size: cover;
            background-position: center;
            min-height: 100%;
        }

        .mfp-close {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            background: none;
            border: none;
            color: #333;
            cursor: pointer;
            z-index: 10000;
        }

        @media (max-width: 768px) {
            .newsletter-popup {
                flex-direction: column;
            }

            .newsletter-popup-image {
                height: 200px;
                width: 100%;
            }
        }
    </style>

@if ($popupData && $showPopup)
        <div class="newsletter-popup">
            <div class="newsletter-content">
                <h2>{{ $popupData->title ?? 'SUBSCRIBE TO NEWSLETTER' }}</h2>
                <p>{{ $popupData->description ?? 'Subscribe to receive updates on new arrivals, special offers and promotions.' }}</p>

                @if ($popupData->email_needed)
                    <form wire:submit.prevent="submitEmail">
                        <div class="newsletter-form">
                            <input type="email" wire:model.defer="email" placeholder="Your email address" required>
                            <button type="submit">SUBMIT</button>
                        </div>
                        @error('email') <span style="color:red">{{ $message }}</span> @enderror
                    </form>
                @else
                    <div class="mt-4">
                        <a href="{{ $popupData->cta_link }}" class="inline-block px-5 py-2 bg-white text-black rounded-full hover:bg-gray-200 transition border border-gray-300">
                            {{ $popupData->cta_text }}
                        </a>
                    </div>
                @endif

                <div class="newsletter-checkbox mt-2">
                    <label>
                        <input type="checkbox" wire:model="dontShowAgain">
                        Don't show this popup again
                    </label>
                </div>
            </div>

            @if ($popupData->image_path)
                <div class="newsletter-popup-image"
                     style="background-image: url('{{ Storage::url($popupData->image_path) }}');">
                </div>
            @endif

            <button type="button" class="mfp-close" wire:click="closePopup">×</button>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('init-popup', event => {
                // Show the popup after the defined delay
                setTimeout(() => {
                    Livewire.dispatch('show-popup');

                    // Auto-close after duration if greater than 0
                    if (event.detail.duration > 0) {
                        setTimeout(() => {
                            Livewire.dispatch('auto-close-popup');
                        }, event.detail.duration);
                    }

                }, event.detail.delay);
            });
        });
    </script>
</div>
