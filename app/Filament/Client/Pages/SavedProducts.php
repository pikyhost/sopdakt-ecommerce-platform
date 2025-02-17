<?php

namespace App\Filament\Client\Pages;

use App\Models\Product;
use App\Services\ProductActionsService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class SavedProducts extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static string $view = 'filament.pages.saved-products';
    protected static ?string $slug = 'my-wishlist';
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Wishlists');
    }

    public function getHeading(): string|Htmlable
    {
        return __('Wishlists');
    }

    protected static function getTableColumns(): array
    {
        return [
            SpatieMediaLibraryImageColumn::make('feature_product_image')
                ->circular()
                ->label(__('Product Image'))
                ->simpleLightbox()
                ->toggleable(false)
                ->collection('feature_product_image'),

            TextColumn::make('name')
                ->toggleable(false)
                ->weight(FontWeight::Bold)
                ->label(__('products.Product Name')),

            TextColumn::make('discount_price_for_current_country')
                ->toggleable(false)
                ->label(__('After Discount Price'))
                ->formatStateUsing(fn ($state) => $state ?? __('No discount available now'))
                ->color(fn ($state) => $state ? 'success' : 'gray') // Green if discount exists, gray otherwise
                ->badge(),

            TextColumn::make('saved_at')
                ->label(__('saved_products.created_at'))
                ->toggleable(false)
                ->sortable()
                ->formatStateUsing(fn (Product $record) => self::getSavedAt($record)),
        ];
    }

    private static function getSavedAt(Product $record)
    {
        if ($record->saved_at) {
            return $record->saved_at->greaterThan(now()->subWeek())
                ? $record->saved_at->diffForHumans()
                : $record->saved_at->format('F j, Y, g:i a');
        }
        return __('saved_products.not_saved_yet');
    }

    public static function table(Table $table, bool $paginationStatus = true): Table
    {
        return $table
            ->actions(
                ActionGroup::make(ProductActionsService::getActions())
                    ->label(__('Actions'))
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            )
            ->poll(null)
            ->filtersFormColumns(4)
            ->emptyStateHeading(__('saved_products.empty_state_heading'))
            ->description(__('saved_products.description'))
            ->query(static::getQuery())
            ->columns(static::getTableColumns())
            ->paginationPageOptions([9, 18, 27])
            ->recordUrl(fn ($record) => url('/'));
    }

    protected static function getQuery(): Builder
    {
        return Product::query()
            ->select('products.*', 'saved_products.user_id', 'saved_products.created_at as saved_at')
            ->join('saved_products', 'products.id', '=', 'saved_products.product_id')
            ->where('saved_products.user_id', auth()->id())
            ->with([
                'usersWhoSaved',
                'category',
                'specialPrices.countryGroup.countries', // Ensure all necessary relationships are loaded
            ])
            ->orderByDesc('saved_at');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('client');
    }
}
