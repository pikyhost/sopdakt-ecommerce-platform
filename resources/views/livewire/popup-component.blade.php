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
    </style>
    @if($popupData && $showPopup)
        <div class="newsletter-popup bg-img" style="background: #f1f1f1 no-repeat center/cover url({{ asset($popupData->image_path) }})">
            <div class="newsletter-popup-content">
                <h2>{{ $popupData->title }}</h2>

                <p>
                    {{ $popupData->description }}
                </p>

                <div class="input-group">
                    <a href="{{ $popupData->cta_link }}" class="btn btn-primary">
                        {{ $popupData->cta_text }}
                    </a>
                </div>

                <div class="newsletter-subscribe">
                    <div class="custom-control custom-checkbox">
                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="show-again"
                            wire:model="dontShowAgain"
                        />
                        <label for="show-again" class="custom-control-label">
                            Don't show this popup again
                        </label>
                    </div>
                </div>
            </div>

            <button
                type="button"
                class="mfp-close"
                wire:click="closePopup"
            >
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
