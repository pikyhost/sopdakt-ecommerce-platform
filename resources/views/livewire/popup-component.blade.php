<div>
    <style>
        .newsletter-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 600px;
            padding: 30px;
            z-index: 9999;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
            border-radius: 5px;
        }

        .newsletter-popup-content {
            position: relative;
            z-index: 1;
            color: #333;
        }

        .mfp-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }

        .newsletter-subscribe {
            margin-top: 15px;
        }

        .mfp-hide {
            display: none !important;
        }
    </style>
    @if($popupData && $showPopup)
        <div class="newsletter-popup mfp-hide bg-img" id="newsletter-popup-form"
             style="background: #f1f1f1 no-repeat center/cover url({{ asset($popupData->image_path) }})">
            <div class="newsletter-popup-content">
                @if($popupData->logo_path)
                    <img src="{{ asset($popupData->logo_path) }}" alt="Logo" class="logo-newsletter" width="111" height="44">
                @endif

                <h2>{{ $popupData->title }}</h2>

                <p>{{ $popupData->description }}</p>

                @if($popupData->has_form)
                    <form action="#">
                        <div class="input-group">
                            <input type="email" class="form-control" id="newsletter-email" name="newsletter-email"
                                   placeholder="Your email address" required />
                            <input type="submit" class="btn btn-primary" value="{{ $popupData->cta_text }}" />
                        </div>
                    </form>
                @else
                    <div class="input-group">
                        <a href="{{ $popupData->cta_link }}" class="btn btn-primary">
                            {{ $popupData->cta_text }}
                        </a>
                    </div>
                @endif

                <div class="newsletter-subscribe">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input"
                               id="show-again" wire:model="dontShowAgain" />
                        <label for="show-again" class="custom-control-label">
                            Don't show this popup again
                        </label>
                    </div>
                </div>
            </div>

            <button title="Close (Esc)" type="button" class="mfp-close" wire:click="closePopup">
                ×
            </button>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('init-popup', event => {
                setTimeout(() => {
                @this.set('showPopup', true);
                }, event.detail.delay);
            });
        });
    </script>
</div>
