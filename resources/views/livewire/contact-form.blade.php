<div>
    @if ($successMessage)
        <div class="custom-alert-success" role="alert">
            <span class="custom-alert-icon">&#10003;</span> {{-- checkmark icon --}}
            {{ $successMessage }}
            <button type="button" class="custom-alert-close" onclick="this.parentElement.style.display='none';" aria-label="Close">&times;</button>
        </div>
    @endif

    <style>
        .custom-alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border-radius: 4px;
            position: relative;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            font-family: sans-serif;
        }

        .custom-alert-icon {
            margin-right: 10px;
            font-weight: bold;
        }

        .custom-alert-close {
            background: none;
            border: none;
            color: #155724;
            font-size: 1.25rem;
            font-weight: bold;
            position: absolute;
            top: 8px;
            right: 12px;
            cursor: pointer;
            line-height: 1;
        }

    </style>

    <form wire:submit.prevent="submit">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group required-field">
                    <label for="contact-name">{{ __('Name') }}</label>
                    <input type="text" class="form-control" id="contact-name" wire:model="name" required>
                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                </div><!-- End .form-group -->

                <div class="form-group required-field">
                    <label for="contact-name">{{ __('Phone') }}</label>
                    <input type="text" class="form-control" id="contact-name" wire:model="phone" required>
                    @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                </div><!-- End .form-group -->

                <div class="form-group required-field">
                    <label for="contact-email">{{ __('Email') }}</label>
                    <input type="email" class="form-control" id="contact-email" wire:model="email" required>
                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                </div><!-- End .form-group -->

                <div class="form-group">
                    <label for="contact-subject">{{ __('Subject') }}</label>
                    <input type="text" class="form-control" id="contact-subject" wire:model="subject">
                    @error('subject') <span class="text-danger">{{ $message }}</span> @enderror
                </div><!-- End .form-group -->
            </div>

            <div class="col-lg-6">
                <div class="form-group required-field mb-0">
                    <label for="contact-message">{{ __('Message') }}</label>
                    <textarea cols="30" rows="1" id="contact-message" class="form-control"
                              wire:model="message" required></textarea>
                    @error('message') <span class="text-danger">{{ $message }}</span> @enderror
                </div><!-- End .form-group -->

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.remove>{{ __('Send Message') }}</span>
                        <span wire:loading>{{ __('Sending...') }}</span>
                    </button>
                </div><!-- End .form-footer -->
            </div>
        </div>
    </form>
</div>
