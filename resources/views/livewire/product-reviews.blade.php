<div>
    <h3 class="reviews-title">{{ $reviews->count() }} reviews for {{ $product->name }}</h3>

    <div class="comment-list">
        @foreach ($reviews as $review)
            <div class="comments">
                <figure class="img-thumbnail">
                    <img src="{{ asset('assets/images/blog/author.jpg') }}" alt="author" width="80" height="80">
                </figure>

                <div class="comment-block">
                    <div class="comment-header">
                        <div class="comment-arrow"></div>

                        <div class="ratings-container float-sm-right">
                            <div class="product-ratings">
                                <span class="ratings" style="width:{{ $review->rating * 20 }}%"></span>
                            </div>
                        </div>

                        <span class="comment-by">
                            <strong>{{ $review->name }}</strong> – {{ $review->created_at->format('M d, Y') }}
                        </span>
                    </div>

                    <div class="comment-content">
                        <p>{{ $review->comment }}</p>
                    </div>

                    @if (Auth::id() === $review->user_id)
                        <button wire:click="editReview({{ $review->id }})" class="btn btn-sm btn-warning">Edit</button>
                        <button wire:click="deleteReview({{ $review->id }})" class="btn btn-sm btn-danger">Delete</button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="divider"></div>

    <div class="add-product-review">
        <h3 class="review-title">{{ $editingReviewId ? 'Edit' : 'Add' }} a Review</h3>

        <form wire:submit.prevent="{{ $editingReviewId ? 'updateReview' : 'addReview' }}">
            <div class="rating-form">
                <label for="rating">Your rating <span class="required">*</span></label>
                <select wire:model="rating" id="rating" required class="form-control">
                    <option value="">Rate…</option>
                    <option value="5">Perfect</option>
                    <option value="4">Good</option>
                    <option value="3">Average</option>
                    <option value="2">Not that bad</option>
                    <option value="1">Very poor</option>
                </select>
                @error('rating') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Your review <span class="required">*</span></label>
                <textarea wire:model="comment" cols="5" rows="6" class="form-control"></textarea>
                @error('comment') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                {{ $editingReviewId ? 'Update' : 'Submit' }}
            </button>
        </form>
    </div>
</div>
