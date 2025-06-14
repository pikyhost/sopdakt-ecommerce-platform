<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\InventoryResource\Pages;
use App\Models\ContactMessage;
use App\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;

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
                ])->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('product.feature_product_image')
                    ->simpleLightbox()
                    ->circular()
                    ->label(__('Product Image'))
                    ->toggleable()
                    ->collection('feature_product_image'),
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('Product'))
                    ->formatStateUsing(function ($record) {
                        return $record->product->name.' (#'.$record->product_id.')';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.sku')
                    ->label(__('SKU'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.quantity')
                    ->label(__('Total Quantity'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id')
                    ->label(__('Variants Information'))
                    ->formatStateUsing(function ($record) {
                        return $record->product->productColors
                            ->flatMap(function ($productColor) {
                                return $productColor->productColorSizes->map(function ($size) use ($productColor) {
                                    $colorName = $productColor->color->name ?? 'N/A';
                                    $sizeName = is_array($size->size->name ?? null)
                                        ? ($size->size->name['en'] ?? 'N/A')
                                        : ($size->size->name ?? 'N/A');
                                    return "{$colorName} → {$sizeName} = {$size->quantity}";
                                });
                            })
                            ->implode('<br>');
                    })
                    ->html()
                    ->sortable()
                    ->toggleable(),

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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()->schema([
                \Filament\Infolists\Components\Split::make([
                    Grid::make(2)->schema([
                        Group::make([
                            TextEntry::make('product.name')
                                ->url(function (Inventory $record) {
                                    return ProductResource::getUrl('edit', ['record' => $record->product->slug]);
                                }, true)
                                ->label(__('Product Name')),

                            TextEntry::make('product.sku')
                                ->copyable()
                                ->weight(FontWeight::Bold)
                                ->label(__('SKU')),
                        ]),
                        Group::make([
                            TextEntry::make('quantity')
                                ->label(__('Total Quantity')),

                          IconEntry::make('product.is_published')
                              ->boolean()
                              ->label(__("Is Active?"))
                        ]),
                    ]),
                    SpatieMediaLibraryImageEntry::make('product.feature_product_image')
                        ->simpleLightbox()
                        ->label(__('Product Image'))
                        ->collection('feature_product_image') ->hiddenLabel()
                        ->grow(false),
                ])->from('xl'),
            ]),

                Section::make(__('Product Availability (Colors, Sizes and Quantities)'))
                    ->schema([
                        RepeatableEntry::make('product.productColors')
                            ->placeholder(__('There are no different colors or sizes.'))
                            ->grid(2)
                            ->hiddenLabel()
                            ->schema([
                                ColorEntry::make('color.code')
                                    ->helperText(fn($record) => $record->color->name ?? 'N/A')
                                    ->hiddenLabel(),

                                RepeatableEntry::make('productColorSizes')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('size.name')
                                            ->formatStateUsing(fn($state) => is_array($state) ? ($state['en'] ?? 'N/A') : $state) // safeguard
                                            ->weight(FontWeight::Bold)
                                            ->hiddenLabel(),

                                        TextEntry::make('quantity')
                                            ->formatStateUsing(fn($state) => $state ?? '0')
                                            ->badge()
                                            ->hiddenLabel(),
                                    ])
                                    ->columns(2),
                            ])
                            ->columns(1)
                            ->columnSpanFull(),
                    ]),

                Section::make()
                    ->hiddenLabel()
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

    public static function getEloquentQuery()
    {
        return parent::getEloquentQuery()
            ->with(['product', 'product.productColors.color', 'product.productColors.productColorSizes.size']);
    }

}
