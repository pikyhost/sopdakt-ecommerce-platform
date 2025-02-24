<?php

namespace App\Filament\Resources;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\Product;
use Filament\Actions\ActionGroup;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
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
use Mokhosh\FilamentRating\Entries\RatingEntry;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;

class ProductRatingResource extends Resource
{
    protected static ?string $model = ProductRating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $slug = 'products-reviews';

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
                SpatieMediaLibraryImageColumn::make('product.feature_product_image')
                    ->simpleLightbox()
                    ->circular()
                    ->label(__('Product Image'))
                    ->toggleable()
                    ->collection('feature_product_image'),

                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('products.Product Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->formatStateUsing(function ($record) {
                        return $record->user->name.' (#'.$record->user_id.')';
                    })
                    ->searchable()
                    ->label(__('User Name'))

                    ->searchable(),

                RatingColumn::make('rating')
                    ->color('warning')
                    ->label(__('product_ratings.rating'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('comment')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    })
                    ->searchable()
                    ->placeholder('-')
                    ->label(__('comments.comment')),

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

                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label(__('Select User')),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->color('primary'),
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
                    ->label(__('Actions'))
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
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
    }

    private static function rejectReview(ProductRating $record)
    {
        // Mark the product rating as pending
        $record->update(['status' => 'rejected']);
    }


    private static function approveReview(ProductRating $record)
    {
        // Approve the product rating
        $record->update(['status' => 'approved']);

        // Update product rating average
        $averageRating = ProductRating::where('product_id', $record->product_id)
            ->where('status', 'approved')
            ->average('rating');

        // Update the product's fake average rating
        Product::where('id', $record->product_id)->update([
            'fake_average_rating' => $averageRating,
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['product', 'user']);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()->schema([
                \Filament\Infolists\Components\Split::make([
                    Grid::make(2)->schema([
                        Group::make([
                            TextEntry::make('product.name')
                                ->label(__('products.Product Name')),

                            RatingEntry::make('rating')
                                ->color('warning')
                                ->label(__('product_ratings.rating')),

                            TextEntry::make('created_at')
                                ->label(__('Creation date'))
                                ->dateTime('D, M j, Y \a\t g:i A'),
                        ]),
                        Group::make([
                            TextEntry::make('user.name')
                                ->formatStateUsing(function ($record) {
                                    return $record->user->name.' (#'.$record->user_id.')';
                                })
                                ->label(__('User Name')),

                            TextEntry::make('status')
                                ->badge()
                                ->label(__('product_ratings.status'))
                                ->label(__('product_ratings.status')),

                            TextEntry::make('updated_at')
                                ->label(__('Last modified at'))
                                ->dateTime('D, M j, Y \a\t g:i A'),
                        ]),
                    ]),
                    SpatieMediaLibraryImageEntry::make('product.feature_product_image')
                        ->simpleLightbox()
                        ->circular()
                        ->hiddenLabel()
                        ->grow(false)
                        ->collection('feature_product_image'),
                ])->from('xl'),

                Section::make(__('comments.comment'))
                    ->label(__('comments.comment'))
                    ->schema([
                        TextEntry::make('comment')
                            ->placeholder(__('user not wrote any comment'))
                            ->prose()
                            ->markdown()
                            ->hiddenLabel(),
                    ])->collapsible(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductRatings::route('/'),
            'view' => Pages\ViewProductRatings::route('/{record}'),
        ];
    }
}
