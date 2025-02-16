<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductActionsService;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class SavedProducts extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;


    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static string $view = 'filament.pages.saved-products';
    protected static ?string $slug = 'wishlists';
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Wishlists');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('user_experience'); // User Experience
    }

    public static function getModelLabel(): string
    {
        return __('Wishlists');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Wishlists');
    }

    protected function getTableHeader()
    {
        return __('Wishlists');
    }

    public function getHeading(): string|Htmlable
    {
        return __('Wishlists');
    }

    public static function getLabel(): ?string
    {
        return __('Wishlists');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Wishlists');
    }

    protected static function getTableColumns(): array
    {
        return [
            SpatieMediaLibraryImageColumn::make('main_product_image')
                ->simpleLightbox()
                ->circular()
                ->label(__('Product Image'))
                ->toggleable(false)
                ->collection('main_product_image'),

            TextColumn::make('name')
                ->toggleable(false)
                ->weight(FontWeight::Bold)
                ->label(__('products.Product Name')),

            TextColumn::make('saver_id')
                ->formatStateUsing(function ($state) {
                   $saverName = User::find($state)->name;
                    return $saverName.' (#'.$state.')';
                })
                ->toggleable(false)
                ->label(__('User Name')),

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

    public static function getProductTablePaginationOptions(): array
    {
        return [9, 18, 27];
    }

    protected static function getEmptyStateHeading(): string
    {
        return __('saved_products.empty_state_heading');
    }

    protected static function getDescription(): string
    {
        if (auth()->user()->hasRole(UserRole::Client->value)) {
            return __('saved_products.description');
        } else {
            return __('saved_products_admin.description');
        }
    }

    protected static function getTableFilters(): array
    {
             return [
                 // User Filter
                 Filter::make('user_id')
                     ->columnSpan(2)
                     ->form([
                         Select::make('user')
                             ->label(__('Select User'))
                             ->options(User::pluck('name', 'id')->toArray())
                             ->searchable()
                             ->preload()
                     ])
                     ->query(function (Builder $query, array $data): Builder {
                         return $query->when(
                             $data['user'] ?? null,
                             fn (Builder $query, $userId) => $query->where('saved_products.user_id', $userId)
                         );
                     })
                     ->indicateUsing(function (array $data): ?string {
                         if (!isset($data['user'])) {
                             return null;
                         }
                         $user = User::find($data['user']);
                         return $user ? __('Filtered by user: ') . $user->name : null;
                     }),

                 // Product Filter
                 Filter::make('product_id')
                     ->columnSpan(2)
                     ->form([
                         Select::make('product')
                             ->label(__('Select Product'))
                             ->options(Product::pluck('name', 'id')->toArray())
                             ->searchable()
                             ->preload()
                     ])
                     ->query(function (Builder $query, array $data): Builder {
                         return $query->when(
                             $data['product'] ?? null,
                             fn (Builder $query, $productId) => $query->where('products.id', $productId)
                         );
                     })
                     ->indicateUsing(function (array $data): ?string {
                         if (!isset($data['product'])) {
                             return null;
                         }
                         $product = Product::find($data['product']);
                         return $product ? __('Filtered by product: ') . $product->name : null;
                     }),
        ];
    }

    public static function table(Table $table, bool $paginationStatus = true): Table
    {
        return $table
            ->filtersFormColumns(4)
            ->actions(
                ActionGroup::make(ProductActionsService::getActions())
                    ->label(__('Actions'))
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(\Filament\Support\Enums\ActionSize::Small)
                    ->color('primary')
                    ->button(),
            )
            ->poll(null)
            ->filtersFormColumns(4)
            ->emptyStateHeading(static::getEmptyStateHeading())
            ->description(static::getDescription())
            ->query(static::getQuery())
            ->columns(static::getTableColumns())
            ->filters(static::getTableFilters())
            ->paginationPageOptions(self::getProductTablePaginationOptions())
            ->recordUrl(fn ($record) => url('/'));
    }

    public static function canAccess(): bool
    {
        return 1;
    }

    protected static function getQuery()
    {
        return Product::query()
            ->select('products.*', 'saved_products.user_id as saver_id', 'saved_products.created_at as saved_at')
            ->join('saved_products', 'products.id', '=', 'saved_products.product_id')
            ->with(['usersWhoSaved', 'category']) // Ensure relationships are loaded
            ->orderByDesc('saved_at')
            ->addSelect(['saved_at' => DB::raw('CAST(saved_products.created_at AS DATETIME)')]); // Ensure it's treated as a DateTime
    }



}
