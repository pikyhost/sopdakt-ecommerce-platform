<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingCostResource\Pages;
use App\Models\ShippingCost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShippingCostResource extends Resource
{
    protected static ?string $model = ShippingCost::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function getNavigationLabel(): string
    {
        return __('shipping_cost.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Shipping Management'); //Products Management
    }

    public static function getModelLabel(): string
    {
        return __('shipping_cost.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('shipping_cost.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('product_id')
                        ->label(__('shipping_cost.product'))
                        ->relationship('product', 'name')
                        ->required(),

                    Forms\Components\Select::make('shipping_type_id')
                        ->label(__('shipping_cost.shipping_type'))
                        ->relationship('shippingType', 'name')
                        ->required(),

                    Forms\Components\Select::make('city_id')
                        ->label(__('shipping_cost.city'))
                        ->relationship('city', 'name')
                        ->nullable()
                        ->live()
                        ->hidden(fn ($get) => $get('governorate_id') || $get('shipping_zone_id') || $get('country_id') || $get('country_group_id')),

                    Forms\Components\Select::make('governorate_id')
                        ->label(__('shipping_cost.governorate'))
                        ->relationship('governorate', 'name')
                        ->nullable()
                        ->live()
                        ->hidden(fn ($get) => $get('city_id') || $get('shipping_zone_id') || $get('country_id') || $get('country_group_id')),

                    Forms\Components\Select::make('shipping_zone_id')
                        ->label(__('shipping_zone.name'))
                        ->relationship('shippingZone', 'name')
                        ->nullable()
                        ->live()
                        ->hidden(fn ($get) => $get('city_id') || $get('governorate_id') || $get('country_id') || $get('country_group_id')),

                    Forms\Components\Select::make('country_id')
                        ->label(__('shipping_cost.country'))
                        ->relationship('country', 'name')
                        ->nullable()
                        ->live()
                        ->hidden(fn ($get) => $get('city_id') || $get('governorate_id') || $get('shipping_zone_id') || $get('country_group_id')),

                    Forms\Components\Select::make('country_group_id')
                        ->label(__('shipping_cost.country_group'))
                        ->relationship('countryGroup', 'name')
                        ->nullable()
                        ->live()
                        ->hidden(fn ($get) => $get('city_id') || $get('governorate_id') || $get('shipping_zone_id') || $get('country_id')),

                    Forms\Components\TextInput::make('cost')
                        ->label(__('shipping_cost.cost'))
                        ->required()
                        ->numeric()
                        ->default(0),

                    Forms\Components\TextInput::make('shipping_estimate_time')
                        ->label(__('shipping_cost.shipping_estimate_time'))
                        ->required()
                        ->maxLength(255)
                        ->default('0-0'),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('shipping_cost.product'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('shippingType.name')
                    ->label(__('shipping_cost.shipping_type'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city.name')
                    ->label(__('shipping_cost.city'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('governorate.name')
                    ->label(__('shipping_cost.governorate'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('shippingZone.name')
                    ->label(__('shipping_zone.name'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('country.name')
                    ->label(__('shipping_cost.country'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('countryGroup.name')
                    ->label(__('shipping_cost.country_group'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('cost')
                    ->label(__('shipping_cost.cost'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('shipping_estimate_time')
                    ->label(__('shipping_cost.shipping_estimate_time'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('shipping_cost.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('shipping_cost.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShippingCosts::route('/'),
            'create' => Pages\CreateShippingCost::route('/create'),
            'edit' => Pages\EditShippingCost::route('/{record}/edit'),
        ];
    }
}
