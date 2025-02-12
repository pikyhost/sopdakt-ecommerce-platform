<?php

namespace App\Filament\Resources;

use App\Enums\Status;
use App\Models\CustomFilamentComment;
use App\Models\Product;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductRatingResource\Pages;
use App\Models\ProductRating;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Mokhosh\FilamentRating\Columns\RatingColumn;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;

class ProductRatingResource extends Resource
{
    protected static ?string $model = ProductRating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('user_experience'); // User Experience
    }

    protected static function getEmptyStateHeading(): string
    {
        return __('saved_products.empty_state_heading');
    }

    protected static function getDescription(): string
    {
        return __('products_reviews.description');
    }

    public static function getNavigationLabel(): string
    {
        return __('reviewed_products.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('reviewed_products.model_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('reviewed_products.plural_label');
    }

    protected function getTableHeader()
    {
        return __('reviewed_products.table_header');
    }

    public function getHeading(): string|Htmlable
    {
        return __('reviewed_products.heading');
    }

    public static function getLabel(): ?string
    {
        return __('reviewed_products.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('reviewed_products.plural_model_label');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('product.main_product_image')
                    ->simpleLightbox()
                    ->circular()
                    ->label(__('Product Image'))
                    ->toggleable()
                    ->collection('main_product_image'),

                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('products.Product Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->label(__('User Name'))

                    ->searchable(),

                RatingColumn::make('rating')
                    ->color('warning')
                    ->label(__('product_ratings.rating'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_comment')
                    ->placeholder('-')
                    ->label(__('comments.comment'))
                    ->limit(50)
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label(__('product_ratings.status'))
                    ->label(__('product_ratings.status'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(true, true)
                    ->label(__('product_ratings.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('product_ratings.status'))
                    ->options([
                        'pending' => __('product_ratings.status_pending'),
                        'approved' => __('product_ratings.status_approved'),
                        'rejected' => __('product_ratings.status_rejected'),
                    ]),

                SelectFilter::make('user_id')->relationship('user', 'name')  ->label(__('Select User')),
                SelectFilter::make('product_id')->relationship('product', 'name') ->label(__('Select Product')),

                Filter::make('rating')
                    ->label(__('Rating'))
                    ->form([
                        Select::make('rating')
                            ->label(__('Rating'))
                            ->options([
                                '1' => '⭐ 1',
                                '2' => '⭐⭐ 2',
                                '3' => '⭐⭐⭐ 3',
                                '4' => '⭐⭐⭐⭐ 4',
                                '5' => '⭐⭐⭐⭐⭐ 5',
                            ]),
                    ])
                    ->query(fn (Builder $query, array $data) =>
                    $query->when($data['rating'] ?? null, fn (Builder $query, $rating) =>
                    $query->where('rating', $rating)
                    )
                    ),
                DateFilter::make('created_at')   ->label(__('Creation date')),


            ])
            ->actions([
                Action::make('approve')
                    ->hidden(fn($record) => $record->status === Status::Approved) // Use enum comparison
                    ->label(__('Approve'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn ($record) => self::approveReview($record)),

                Action::make('reject')
                    ->label(__('Reject'))
                    ->hidden(fn($record) => $record->status === Status::Rejected) // Use enum comparison
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(fn ($record) => self::rejectReview($record)),

                Action::make('pending')
                    ->label(__('Pending'))
                    ->hidden(fn($record) => $record->status === Status::Pending) // Use enum comparison
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->action(fn ($record) => self::pendingReview($record)),

                Tables\Actions\DeleteAction::make()->color('gray'),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\BulkAction::make('approve')
                        ->label(__('Approve Selected'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn ($record) => self::approveReview($record));
                        }),

                    \Filament\Tables\Actions\BulkAction::make('reject')
                        ->label(__('Reject Selected'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn ($record) => self::rejectReview($record));
                        }),

                    \Filament\Tables\Actions\BulkAction::make('pending')
                        ->label(__('Mark as Pending'))
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn ($record) => self::pendingReview($record));
                        }),

                   DeleteBulkAction::make()->color('gray'),
                ])
            ])
            ->defaultSort('created_at', 'desc');
    }

    private static function pendingReview(ProductRating $record)
    {
        // Mark the product rating as pending
        $record->update(['status' => 'pending']);

        // Mark the related comment as pending
        CustomFilamentComment::where('subject_id', $record->product_id)
            ->where('subject_type', Product::class)
            ->where('user_id', $record->user_id)
            ->update(['status' => 'pending']);
    }


    private static function approveReview(ProductRating $record)
    {
        // Approve the product rating
        $record->update(['status' => 'approved']);

        // Approve the related comment
        CustomFilamentComment::where('subject_id', $record->product_id)
            ->where('subject_type', Product::class)
            ->where('user_id', $record->user_id)
            ->update(['status' => 'approved']);

        // Update product rating average
        $averageRating = ProductRating::where('product_id', $record->product_id)
            ->where('status', 'approved')
            ->average('rating');

        // Update the product's fake average rating
        Product::where('id', $record->product_id)->update([
            'fake_average_rating' => $averageRating,
        ]);
    }

    private static function rejectReview(ProductRating $record)
    {
        // Reject the product rating
        $record->update(['status' => 'rejected']);

        // Reject the related comment
        CustomFilamentComment::where('subject_id', $record->product_id)
            ->where('subject_type', Product::class)
            ->where('user_id', $record->user_id)
            ->update(['status' => 'rejected']);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->select([
                'product_ratings.*',
                'filament_comments.comment as user_comment',
                'filament_comments.status as comment_status',
            ])
            ->leftJoin('filament_comments', function ($join) {
                $join->on('product_ratings.product_id', '=', 'filament_comments.subject_id')
                    ->where('filament_comments.subject_type', Product::class)
                    ->whereRaw('filament_comments.user_id = product_ratings.user_id');
            })
            ->with(['product', 'user']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductRatings::route('/'),
        ];
    }
}
