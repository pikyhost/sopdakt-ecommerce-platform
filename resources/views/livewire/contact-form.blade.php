<div>
    <form wire:submit.prevent="submit">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group required-field">
                    <label for="contact-name">{{ __('Name') }}</label>
                    <input type="text" class="form-control" id="contact-name" wire:model="name" required>
                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
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
