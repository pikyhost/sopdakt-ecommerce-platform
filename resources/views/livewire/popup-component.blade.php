<div>
    <style>
        /* Base Styles */
        .newsletter-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: row;
            width: 90%;
            max-width: 800px;
            min-height: 400px;
            z-index: 9999;
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            animation: fadeInUp 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid #e0e0e0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate(-50%, -40%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }

        /* Content Area */
        .newsletter-content {
            flex: 1;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .newsletter-content h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #000;
            line-height: 1.3;
        }

        .newsletter-content p {
            color: #555;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* Form Styles */
        .popup-form {
            margin-top: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .form-group {
            position: relative;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            width: 20px;
            height: 20px;
            color: #888;
            transition: all 0.3s ease;
        }

        .email-input {
            padding: 14px 16px 14px 48px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 15px;
            width: 100%;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
            color: #333;
        }

        .email-input:focus {
            border-color: #888;
            box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
            outline: none;
            background-color: #fff;
        }

        .email-input:focus + .input-icon {
            color: #000;
        }

        .popup-error {
            color: #d32f2f;
            font-size: 13px;
            margin-top: 6px;
            padding-left: 8px;
        }

        .popup-success {
            color: #388e3c;
            font-size: 14px;
            margin-top: 4px;
        }

        /* Button Styles */
        .submit-button {
            position: relative;
            padding: 14px 24px;
            background-color: #000;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            overflow: hidden;
        }

        .submit-button:hover {
            background-color: #333;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .submit-button:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .button-icon {
            width: 18px;
            height: 18px;
            transition: transform 0.3s ease;
            fill: white;
        }

        .submit-button {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background-color: black; /* blue-700 */
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .submit-button .button-icon {
            margin-left: 0.5rem;
            transition: transform 0.3s ease;
        }

        .submit-button:hover {
            background-color: black; /* blue-600 */
            color: white;
        }

        .submit-button:hover .button-icon {
            transform: translateX(4px);
        }


        .button-loader {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .button-loader svg {
            width: 24px;
            height: 24px;
        }

        .button-loader circle {
            stroke: white;
        }

        .submit-button.loading .button-text,
        .submit-button.loading .button-icon {
            opacity: 0;
        }

        .submit-button.loading .button-loader {
            opacity: 1;
        }

        /* CTA Button (Optimized) */

        /* Mobile tweaks */
        /* Checkbox */
        .newsletter-checkbox {
            font-size: 13px;
            color: #555;
            display: flex;
            align-items: center;
            margin-top: 20px;
        }

        .newsletter-checkbox input {
            margin-right: 10px;
            accent-color: #000;
        }

        /* Image Container */
        .newsletter-popup-image-container {
            flex: 1;
            position: relative;
            overflow: hidden;
            background-color: #f5f5f5;
        }

        .newsletter-popup-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .newsletter-popup-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 100%);
        }

        /* Close Button */
        .mfp-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: rgba(255,255,255,0.8);
            border: none;
            color: #333;
            cursor: pointer;
            z-index: 10000;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .mfp-close:hover {
            background: rgba(255,255,255,1);
            transform: rotate(90deg);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .newsletter-popup {
                flex-direction: column;
                max-width: 95%;
                min-height: auto;
            }

            .newsletter-content {
                padding: 30px 25px;
                order: 2;
            }

            .newsletter-popup-image-container {
                height: 250px;
                order: 1;
            }

            .newsletter-content h2 {
                font-size: 24px;
            }

            .newsletter-content p {
                font-size: 15px;
            }
        }
    </style>

    @if($showPopup && isset($popupData))
        <div class="newsletter-popup" wire:key="popup-{{ $popupData->id }}">
            <div class="newsletter-content">
                <h2>{{ $popupData->title ?? 'SUBSCRIBE TO NEWSLETTER' }}</h2>
                <p>{{ $popupData->description ?? 'Subscribe to receive updates on new arrivals, special offers and promotions.' }}</p>

                @if(session()->has('message'))
                    <p class="popup-success">{{ session('message') }}</p>
                @endif

                @if($popupData->email_needed)
                    <form wire:submit.prevent="submitEmail" class="popup-form">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <!-- Email SVG -->
                                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" />
                                    <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" />
                                </svg>
                                <input
                                    type="email"
                                    wire:model.defer="email"
                                    placeholder="Your email address"
                                    required
                                    class="email-input"
                                    aria-label="Email address"
                                >
                            </div>
                            @error('email')
                            <p class="popup-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="submit-button">
                            <span class="button-text">Subscribe Now</span>
                            <svg class="button-icon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </form>
                @else
                    <a href="{{ $popupData->cta_link }}" class="submit-button" wire:click="closePopup">
                        {{ $popupData->cta_text ?? 'Shop Now' }}
                    </a>
                @endif

                <div class="newsletter-checkbox">
                    <label>
                        <input type="checkbox" wire:model="dontShowAgain"> Don't show this popup again
                    </label>
                </div>
            </div>

            @if($popupData->image_path)
                <div class="newsletter-popup-image-container">
                    <img
                        src="{{ Storage::url($popupData->image_path) }}"
                        alt="Newsletter promotion"
                        class="newsletter-popup-image"
                        loading="lazy"
                        width="400"
                        height="400"
                        srcset="{{ Storage::url($popupData->image_path) }} 400w,
                        {{ Storage::url($popupData->image_path) }} 800w"
                        sizes="(max-width: 768px) 100vw, 50vw"
                    >
                    <div class="newsletter-popup-image-overlay"></div>
                </div>
            @endif

            <button type="button" class="mfp-close" wire:click="closePopup">×</button>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let showTimeout, hideTimeout;

            Livewire.on('init-popup', ({ delay = 5000, duration = 30000 }) => {
                clearTimeout(showTimeout);
                clearTimeout(hideTimeout);

                console.log(`Popup scheduled in ${delay / 1000}s for ${duration / 1000}s`);

                showTimeout = setTimeout(() => {
                    Livewire.dispatch('show-popup');

                    if (duration > 0) {
                        hideTimeout = setTimeout(() => {
                            Livewire.dispatch('close-popup');
                        }, duration);
                    }
                }, delay);
            });
        });
    </script>

</div>
