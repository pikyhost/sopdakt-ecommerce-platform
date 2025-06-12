<div>
    <h3 class="reviews-title">{{ __('messages.reviews_for') }} {{ $product->name }}</h3>

    {{-- Toast Notification --}}
    <div x-data="{ show: false, message: '', type: '' }"
         x-show="show"
         class="alert"
         :class="type === 'success' ? 'alert-success' : 'alert-danger'"
         x-transition
         x-init="
            window.addEventListener('notify', event => {
                message = event.detail.message;
                type = event.detail.type;
                show = true;
                setTimeout(() => show = false, 3000);
            });
         ">
        <p x-text="message"></p>
    </div>

    {{-- Reviews List --}}
    <div class="comment-list">
        @foreach ($reviews as $review)
            <div class="comments">
                <figure class="img-thumbnail">
                    <img src="{{ $review->user->avatar_url
                        ? (Str::startsWith($review->user->avatar_url, 'http') ? $review->user->avatar_url : Storage::url($review->user->avatar_url))
                        : asset('assets/images/clients/client1.png') }}"
                         alt="author" width="80" height="80">
                </figure>
                <div class="comment-block">
                    <div class="comment-header">
                        <span class="comment-by">
                            <strong>{{ $review->user->name }}</strong> – {{ $review->created_at->format('F d, Y') }}
                        </span>
                        <div class="ratings-container float-sm-right">
                            <div class="product-ratings">
                                <span class="ratings" style="width:{{ $review->rating * 20 }}%"></span>
                            </div>
                            @php
                                $status = $review->status instanceof App\Enums\Status ? $review->status->value : $review->status;
                            @endphp
                            @if($status === App\Enums\Status::Pending->value)
                                <span class="badge badge-warning">{{ __('messages.pending') }}</span>
                            @elseif($status === App\Enums\Status::Rejected->value)
                                <span class="badge badge-danger">{{ __('messages.rejected') }}</span>
                            @endif
                        </div>

                    </div>
                    <div class="comment-content">
                        <p>{{ $review->comment }}</p>
                    </div>
                    @if(Auth::check() && (Auth::id() === $review->user_id || Auth::user()->hasRole(['admin', 'super_admin'])))
                        <button wire:click="deleteReview({{ $review->id }})"
                                onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                            {{ __('messages.delete') }}
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Review Form (Only for Authenticated Users) --}}
    @if(Auth::check())
        <div class="add-product-review">
            <h3 class="review-title">{{ __('messages.add_review') }}</h3>
            <form wire:submit.prevent="addReview">
                <div class="rating-form">
                    <label>{{ __('messages.your_rating') }} <span class="required">*</span></label>
                    <select wire:model="rating" required>
                        <option value="">{{ __('messages.rate') }}</option>
                        <option value="5">{{ __('messages.perfect') }}</option>
                        <option value="4">{{ __('messages.good') }}</option>
                        <option value="3">{{ __('messages.average') }}</option>
                        <option value="2">{{ __('messages.not_bad') }}</option>
                        <option value="1">{{ __('messages.very_poor') }}</option>
                    </select>
                    @error('rating') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>{{ __('messages.your_review') }} <span class="required">*</span></label>
                    <textarea wire:model="comment" cols="5" rows="6" class="form-control form-control-sm" required></textarea>
                    @error('comment') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <input type="submit" class="btn btn-primary" value="{{ __('messages.submit') }}">
            </form>
        </div>
    @else
        <div class="alert alert-warning">
            {{ __('messages.login_to_review') }}
            <a href="{{ url('/client/login') }}" class="text-primary">{{ __('messages.login_here') }}</a>
        </div>
    @endif
</div>
