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
        /* Premium CTA Button (Ultra Clean Black & White Style) */
        .premium-cta-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 2.5rem;
            background-color: #000;
            color: #fff;
            border-radius: 50px;
            border: 2px solid transparent;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: all 0.35s ease;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-top: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Inverted style on hover */
        .premium-cta-button:hover {
            background-color: #fff;
            color: #000;
            border-color: #000;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            transform: translateY(-3px);
        }

        /* Subtle arrow animation */
        .premium-cta-button::after {
            content: "→";
            margin-left: 10px;
            transition: transform 0.3s ease;
        }

        /* Arrow slide effect on hover */
        .premium-cta-button:hover::after {
            transform: translateX(6px);
        }

        /* Optional subtle glowing border on hover */
        .premium-cta-button:hover {
            outline: none;
            box-shadow: 0 0 0 2px #fff, 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* Mobile tweaks */
        @media (max-width: 480px) {
            .premium-cta-button {
                width: 100%;
                padding: 0.85rem 1.5rem;
                font-size: 1rem;
            }
        }

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

    @if($showPopup)
        <div class="newsletter-popup">
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
                                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
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
                            <svg class="button-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            <span class="button-loader">
                                <svg viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg">
                                    <g fill="none" fill-rule="evenodd">
                                        <g transform="translate(1 1)" stroke-width="2">
                                            <circle stroke-opacity=".5" cx="18" cy="18" r="18"/>
                                            <path d="M36 18c0-9.94-8.06-18-18-18">
                                                <animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="1s" repeatCount="indefinite"/>
                                            </path>
                                        </g>
                                    </g>
                                </svg>
                            </span>
                        </button>
                    </form>
                @else
                    <a href="{{ $popupData->cta_link }}" class="premium-cta-button" wire:click="closePopup">
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
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('init-popup', event => {
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('show-popup'));
                }, event.detail.delay);

                if (event.detail.duration > 0) {
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('auto-close-popup'));
                    }, event.detail.delay + event.detail.duration);
                }
            });

            window.addEventListener('auto-close-popup', () => {
                Livewire.dispatch('auto-close-popup');
            });

            window.addEventListener('show-popup', () => {
                Livewire.dispatch('show-popup');
            });

            window.addEventListener('next-popup', () => {
                Livewire.dispatch('next-popup');
            });
        });

        document.addEventListener('livewire:load', function() {
            Livewire.hook('message.processed', (message, component) => {
                const submitButtons = document.querySelectorAll('.submit-button');
                submitButtons.forEach(button => {
                    if (message.component.fingerprint.name === 'popup-component' &&
                        message.updateQueue[0]?.payload.event === 'callMethod' &&
                        message.updateQueue[0]?.payload.method === 'submitEmail') {
                        button.classList.add('loading');
                    } else {
                        button.classList.remove('loading');
                    }
                });
            });
        });
    </script>

</div>
