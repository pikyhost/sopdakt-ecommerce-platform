<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingCostResource\Pages;
use App\Models\City;
use App\Models\Governorate;
use App\Models\ShippingCost;
use App\Models\ShippingZone;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ShippingCostResource extends Resource
{
    protected static ?string $model = ShippingCost::class;

    protected static ?int $navigationSort = -199;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function getNavigationLabel(): string
    {
        return __('shipping_cost.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Shipping & Countries'); //Products Attributes Management
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
        return $form->schema([
            Section::make()->schema([
                Select::make('product_id')
                    ->label(__('shipping_cost.product'))
                    ->relationship('product', 'name')
                    ->required(),

                Select::make('shipping_type_id')
                    ->label(__('shipping_cost.shipping_type'))
                    ->relationship('shippingType', 'name'),

                Select::make('city_id')
                    ->rules([
                        fn (Get $get) => Rule::unique(ShippingCost::class, 'city_id')
                            ->where(fn ($query) => $query
                                ->where('product_id', $get('product_id'))
                                ->whereNull('shipping_zone_id')
                                ->whereNull('country_group_id')
                                ->whereNull('governorate_id')
                                ->whereNull('country_id')
                            )
                            ->ignore(request()->route('record')),
                    ])
                    ->relationship('city', 'name')
                    ->nullable()
                    ->live()
                    ->hidden(fn ($get) => $get('governorate_id') || $get('shipping_zone_id') || $get('country_id') || $get('country_group_id')),

                Select::make('governorate_id')
                    ->rules([
                        fn (Get $get) => Rule::unique(ShippingCost::class, 'governorate_id')
                            ->where(fn ($query) => $query
                                ->where('product_id', $get('product_id'))
                                ->whereNull('shipping_zone_id')
                                ->whereNull('country_group_id')
                                ->whereNull('city_id')
                                ->whereNull('country_id')
                            )
                            ->ignore(request()->route('record')),
                    ])
                    ->relationship('governorate', 'name')
                    ->nullable()
                    ->live()
                    ->hidden(fn ($get) => $get('city_id') || $get('shipping_zone_id') || $get('country_id') || $get('country_group_id')),

                Select::make('shipping_zone_id')
                    ->rules([
                        fn (Get $get) => Rule::unique(ShippingCost::class, 'shipping_zone_id')
                            ->where(fn ($query) => $query
                                ->where('product_id', $get('product_id'))
                                ->whereNull('governorate_id')
                                ->whereNull('country_group_id')
                                ->whereNull('city_id')
                                ->whereNull('country_id')
                            )
                            ->ignore(request()->route('record')),
                    ])
                    ->relationship('shippingZone', 'name')
                    ->nullable()
                    ->live()
                    ->hidden(fn ($get) => $get('city_id') || $get('governorate_id') || $get('country_id') || $get('country_group_id')),

                Select::make('country_id')
                    ->rules([
                        fn (Get $get) => Rule::unique(ShippingCost::class, 'country_id')
                            ->where(fn ($query) => $query
                                ->where('product_id', $get('product_id'))
                                ->whereNull('governorate_id')
                                ->whereNull('shipping_zone_id')
                                ->whereNull('city_id')
                                ->whereNull('country_group_id')
                            )
                            ->ignore(request()->route('record')),
                    ])
                    ->relationship('country', 'name')
                    ->nullable()
                    ->live()
                    ->hidden(fn ($get) => $get('city_id') || $get('governorate_id') || $get('shipping_zone_id') || $get('country_group_id')),

                Select::make('country_group_id')
                    ->rules([
                        fn (Get $get) => Rule::unique(ShippingCost::class, 'country_group_id')
                            ->where(fn ($query) => $query
                                ->where('product_id', $get('product_id'))
                                ->whereNull('governorate_id')
                                ->whereNull('shipping_zone_id')
                                ->whereNull('city_id')
                                ->whereNull('country_id')
                            )
                            ->ignore(request()->route('record')),
                    ])
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
                    ->placeholder('-')
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
                SelectFilter::make('shipping_type_id')
                    ->relationship('shippingType', 'name')
                    ->label(__('shipping_cost.shipping_type')),
                SelectFilter::make('product_id')
                    ->relationship('product', 'name')
                    ->label(__('shipping_cost.product')),
                SelectFilter::make('shipping_type_id')
                    ->relationship('shippingType', 'name')
                    ->label(__('shipping_zone.name')),
                SelectFilter::make('country_group_id')
                    ->relationship('countryGroup', 'name')
                    ->label(__('shipping_cost.country_group')),
                SelectFilter::make('country_id')
                    ->relationship('country', 'name')
                    ->label(__('shipping_cost.country')),
                SelectFilter::make('governorate_id')
                    ->relationship('governorate', 'name')
                    ->label(__('shipping_cost.governorate')),
                SelectFilter::make('city_id')
                    ->relationship('city', 'name')
                    ->label(__('shipping_cost.city')),
                QueryBuilder::make()
                    ->constraints([
                        TextConstraint::make('shipping_estimate_time')
                            ->label(__('shipping_cost.shipping_estimate_time')),
                        NumberConstraint::make('cost')
                            ->label(__('shipping_cost.cost')),
                    ])->columnSpanFull(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShippingCosts::route('/'),
            'create' => Pages\CreateShippingCost::route('/create'),
            'edit' => Pages\EditShippingCost::route('/{record}/edit'),
        ];
    }
}
