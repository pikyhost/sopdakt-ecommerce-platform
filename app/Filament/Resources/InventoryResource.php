<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoryResource extends Resource
{
    use Translatable;

    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function getNavigationGroup(): ?string
    {
        return __('Stock Management'); //Products Management
    }

    public static function getNavigationLabel(): string
    {
        return __('Stock');
    }

    public static function getModelLabel(): string
    {
        return __('Stock');
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('Stock');
    }

    public static function getLabel(): ?string
    {
        return __('Stock');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Stock');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('product_id')
                        ->relationship('product', 'name')
                        ->required(),
                    Forms\Components\TextInput::make('quantity')
                        ->required()
                        ->numeric()
                        ->default(0),
                ])->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('product.feature_product_image')
                    ->circular()
                    ->label(__('Feature Image')),
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('Product'))
                    ->formatStateUsing(function ($record) {
                        return $record->product->name.' (#'.$record->product_id.')';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.sku')
                    ->label(__('SKU'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('Total Quantity'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id')
                    ->limit(90)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    })
                    ->label(__('Variants'))
                    ->formatStateUsing(function ($record) {
                        return $record->product->productColors
                            ->flatMap(function ($productColor) {
                                return $productColor->productColorSizes->map(function ($size) use ($productColor) {
                                    $colorName = $productColor->color->name ?? 'N/A';
                                    $sizeName = $size->size->name ?? 'N/A';
                                    return "{$colorName} → {$sizeName} = {$size->quantity}";
                                });
                            })
                            ->implode('<br>');
                    })->html(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Product Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                SpatieMediaLibraryImageEntry::make('product.feature_product_image')
                                    ->circular()
                                    ->label(__('Feature Image'))
                                    ->columnSpan(1),

                                TextEntry::make('product.name')
                                    ->label(__('Product Name'))
                                    ->columnSpan(1),

                                TextEntry::make('product.sku')
                                    ->label(__('SKU'))
                                    ->columnSpan(1),

                                TextEntry::make('quantity')
                                    ->label(__('Total Quantity'))
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Variants Information')
                    ->schema([
                        TextEntry::make('variants')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                $variants = $record->product->productColors
                                    ->flatMap(function ($productColor) {
                                        return $productColor->productColorSizes->map(function ($size) use ($productColor) {
                                            $colorName = $productColor->color->name ?? 'N/A';
                                            $sizeName = $size->size->name ?? 'N/A';
                                            return "{$colorName} → {$sizeName} = {$size->quantity}";
                                        });
                                    })
                                    ->implode('<br>');

                                return empty($variants) ? 'No variants available' : $variants;
                            })
                            ->html(),
                    ]),

                Section::make('Metadata')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('Created At'))
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label(__('Updated At'))
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventories::route('/'),
            'create' => Pages\CreateInventory::route('/create'),
            'view' => Pages\ViewInventory::route('/{record}'),
            'edit' => Pages\EditInventory::route('/{record}/edit'),
        ];
    }
}
