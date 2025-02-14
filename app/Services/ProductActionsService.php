<?php

namespace App\Services;

use App\Models\CustomFilamentComment;
use App\Models\Product;
use App\Models\ProductRating;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Mokhosh\FilamentRating\Components\Rating;
use Parallax\FilamentComments\Tables\Actions\CommentsAction;

class ProductActionsService
{
    public static function rateProduct(Product $record, array $data): void
    {
        $userId = auth()->id();
        $recordId = $record->id;
        $newRating = intval($data['rating']); // Ensure integer rating

        // Check if the user is an admin or super_admin
        $isAdmin = auth()->user()->hasRole(['admin', 'super_admin']);

        // Set status based on role
        $status = $isAdmin ? 'approved' : 'pending';

        // Find the existing rating
        $existingRating = \App\Models\ProductRating::where('user_id', $userId)
            ->where('product_id', $recordId)
            ->first();

        // Avoid unnecessary updates if the rating and status are unchanged
        if ($existingRating && $existingRating->rating == $newRating && $existingRating->status == $status) {
            Notification::make()
                ->title(__('product.rating.already_saved'))
                ->body(__('product.rating.no_changes'))
                ->info()
                ->send();
            return;
        }

        // Update or create the rating
        \App\Models\ProductRating::updateOrCreate(
            ['user_id' => $userId, 'product_id' => $recordId],
            ['rating' => $newRating, 'status' => $status]
        );

        // Update the fake average rating only if there are approved ratings
        $averageRating = \App\Models\ProductRating::where('product_id', $recordId)
            ->where('status', 'approved')
            ->average('rating');

        // Update only if there's at least one approved rating
        if ($averageRating !== null) {
            $record->update(['fake_average_rating' => $averageRating]);
        }

        Notification::make()
            ->title(__('product.rating.thank_you'))
            ->body(__('product.rating.saved', ['rating' => $newRating]))
            ->success()
            ->send();
    }

    public static function getActions(): array
    {
        return [
            Action::make('view_product_details')
                ->label(__('product.actions.view_details'))
                ->color('gray')
                ->icon('heroicon-m-eye')
                ->openUrlInNewTab()
                ->action(fn (Product $record) => redirect(url('/'))),

            Action::make('rate_and_comment')
                ->color('primary')
                ->visible(fn () => auth()->check())
                ->label(__('product.actions.rate_and_review'))
                ->icon('heroicon-o-star')
                ->modalHeading(fn ($record) => __('product.rating.modal_heading', ['product' => $record->name]))
                ->modalSubmitActionLabel(__('product.rating.confirm'))
                ->form([
                    Rating::make('rating')
                        ->color('warning')
                        ->required()
                        ->label(__('product.rating.your_rating'))
                        ->default(fn ($record) =>
                        ProductRating::where('product_id', $record->id)
                            ->where('user_id', Auth::id())
                            ->value('rating')
                        ),

                    Textarea::make('comment')
                        ->rows(3)
                        ->hiddenLabel()
                        ->helperText(__('Add any notes (optional)'))
                        ->placeholder(__('comments.placeholder'))
                        ->default(fn ($record) =>
                        ProductRating::where('product_id', $record->id)
                            ->where('user_id', Auth::id())
                            ->value('comment')
                        ),
                ])
                ->action(fn ($record, array $data) => self::handleRateAndComment($record, $data))
        ];
    }

    public static function handleRateAndComment(Product $record, array $data): void
    {
        $userId = auth()->id();
        $recordId = $record->id;
        $newRating = intval($data['rating']);
        $comment = $data['comment'] ?? null;

        // Check if the user is an admin or super_admin
        $isAdmin = auth()->user()->hasRole(['admin', 'super_admin']);

        // Set status based on role
        $status = $isAdmin ? 'approved' : 'pending';

        // Save or update the rating & comment
        \App\Models\ProductRating::updateOrCreate(
            ['user_id' => $userId, 'product_id' => $recordId],
            ['rating' => $newRating, 'comment' => $comment, 'status' => $status]
        );

        // Update fake average rating only if there are approved ratings
        $averageRating = \App\Models\ProductRating::where('product_id', $recordId)
            ->where('status', 'approved')
            ->average('rating');

        if ($averageRating !== null) {
            $record->update(['fake_average_rating' => $averageRating]);
        }

        Notification::make()
            ->title(__('product.rating.thank_you'))
            ->body(__('product.rating.saved', ['rating' => $newRating]))
            ->success()
            ->send();
    }

}
