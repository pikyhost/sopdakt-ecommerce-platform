<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms\Components\Section;
use Filament\Resources\RelationManagers\Concerns\Translatable;
use Filament\Resources\RelationManagers\RelationManager;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Reactive;

class BundlesRelationManager extends RelationManager
{
    use Translatable;

    protected static string $relationship = 'bundles';

    #[Reactive]
    public ?string $activeLocale = null;

    protected static bool $isLazy = false;

    /**
     * @param Model $ownerRecord
     * @param string $pageClass
     * @return string
     */
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('bundles.label');
    }

    protected static function getModelLabel(): ?string
    {
        return __('bundles.label');
    }

    protected static function getPluralModelLabel(): ?string
    {
        return __('bundles.label');
    }

    protected static function getPluralRecordLabel(): ?string
    {
        return __('bundles.label');
    }

    protected function updateDiscountPrice(Set $set, Get $get)
    {
        if ($get('bundle_type') === 'buy_x_get_y' && $get('buy_x') !== null && $get('get_y') !== null) {
            $productId = $this->getOwnerRecord()->id;
            $product = \App\Models\Product::find($productId);

            if ($product) {
                $buyX = floatval($get('buy_x')) ?: 1;
                $pricePerUnit = floatval($product->discount_price_for_current_country);
                $discountPrice = $buyX * $pricePerUnit;

                $set('discount_price', $discountPrice);
            }
        } else {
            // Reset discount price if conditions are not met
            $set('discount_price', null);
        }
    }

    protected function handleDiscountUpdated(Set $set, Get $get)
    {
        // If discount_price is manually set in fixed price mode, reset buy_x and get_y
        if ($get('bundle_type') === 'fixed_price' && $get('discount_price') !== null) {
            $set('buy_x', null);
            $set('get_y', null);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label(__('bundles.name'))
                        ->required(),

                    TextInput::make('name_for_admin')
                        ->label(__('bundles.name_for_admin'))
                        ->required(),

                    Select::make('bundle_type')
                        ->live()
                        ->label(__('bundles.type'))
                        ->options([
                            'fixed_price' => __('bundles.fixed_price'),
                            'buy_x_get_y' => __('bundles.buy_x_get_y'),
                            'buy_quantity_fixed_price' => __('type.buy_quantity_fixed_price'), // New type
                        ])
                        ->required()
                        ->afterStateUpdated(fn (Set $set) => $set('discount_price', null)),

                    Hidden::make('products')
                        ->default(fn () => $this->getOwnerRecord()->id)
                        ->visible(fn (Get $get) => $get('bundle_type') !== 'buy_x_get_y'),

                    Select::make('products')
                        ->live()
                        ->required()
                        ->preload()
                        ->searchable()
                        ->maxItems(fn (Get $get) => in_array($get('bundle_type'), ['buy_x_get_y', 'buy_quantity_fixed_price']) ? 1 : 10)
                        ->label(__('bundles.products'))
                        ->multiple()
                        ->relationship('products', 'name')
                        ->rules([
                            fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) {
                                $ownerId = $this->getOwnerRecord()->id;
                                if (!in_array($ownerId, (array) $value)) {
                                    $fail(__('The selected products must include the current product you are editing: :name.', [
                                        'name' => $this->getOwnerRecord()->name
                                    ]));
                                }
                            }
                        ])
                        ->visible(fn (Get $get) =>
                        in_array($get('bundle_type'), ['fixed_price', 'buy_quantity_fixed_price'])
                        ),

                    TextInput::make('buy_x')
                        ->live()
                        ->label(__('bundles.buy_x'))
                        ->numeric()
                        ->visible(fn (Get $get) => $get('bundle_type') === 'buy_x_get_y')
                        ->afterStateUpdated(fn (Set $set, Get $get) => $this->updateDiscountPrice($set, $get)),

                    TextInput::make('get_y')
                        ->live()
                        ->label(__('bundles.get_y_free'))
                        ->numeric()
                        ->visible(fn (Get $get) => $get('bundle_type') === 'buy_x_get_y')
                        ->afterStateUpdated(fn (Set $set, Get $get) => $this->updateDiscountPrice($set, $get)),

                    TextInput::make('buy_quantity')
                        ->live()
                        ->label(__('Quantity'))
                        ->numeric()
                        ->visible(fn (Get $get) => $get('bundle_type') === 'buy_quantity_fixed_price')
                        ->afterStateUpdated(fn (Set $set, Get $get) => $this->updateDiscountPrice($set, $get)),

                    TextInput::make('discount_price')
                        ->live()
                        ->label(__('bundles.discount_price'))
                        ->numeric()
                        ->visible(fn (Get $get) => $get('bundle_type') !== null)
                        ->disabled(fn (Get $get) =>
                            ($get('bundle_type') === 'buy_x_get_y' && $get('buy_x') !== null && $get('get_y') !== null)
                        )
                        ->dehydrated()
                        ->afterStateUpdated(fn (Set $set, Get $get) => $this->handleDiscountUpdated($set, $get)),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\LocaleSwitcher::make(),
            ])
            ->defaultSort('bundles.id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('bundles.name'))
                    ->searchable(),  //name_for_admin

                Tables\Columns\TextColumn::make('name_for_admin')
                    ->label(__('bundles.name_for_admin'))
                    ->searchable(),  //name_for_admin

                Tables\Columns\TextColumn::make('products.name')
                    ->label(__('bundles.products'))
                    ->placeholder('-')
                    ->limitList(2)
                    ->badge(),
                Tables\Columns\TextColumn::make('bundle_type')
                    ->badge()
                    ->label(__('bundles.type')),
                Tables\Columns\TextColumn::make('discount_price')
                    ->placeholder('-')
                    ->label(__('bundles.discount_price'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('buy_quantity')->label(__('Quantity')),
                Tables\Columns\TextColumn::make('buy_x')
                    ->placeholder('-')
                    ->label(__('bundles.buy_x'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('get_y')
                    ->label(__('bundles.get_y_free'))
                    ->numeric()
                    ->sortable()
                    ->placeholder(fn ($record) => $record->buy_x ? __('bundles.discount_price') : '-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
