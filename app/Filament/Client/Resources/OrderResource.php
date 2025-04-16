<?php

namespace App\Filament\Client\Resources;

use App\Enums\OrderStatus;
use App\Filament\Client\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Client\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Client\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Client\Resources\OrderResource\Pages\ViewOrder;
use App\Models\Bundle;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\Setting;
use App\Models\ShippingCost;
use App\Models\ShippingType;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $slug = 'my-orders';

    public static function getNavigationGroup(): ?string
    {
        return __('My orders');
    }

    public static function getNavigationLabel(): string
    {
        return __('List');
    }

    public static function getModelLabel(): string
    {
        return __('order');
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('landing_page_order.orders');
    }

    public static function getLabel(): ?string
    {
        return __('order');
    }

    public static function getPluralModelLabel(): string
    {
        return __('landing_page_order.orders');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->copyable()
                    ->formatStateUsing(fn($state) => '#' . $state)
                    ->label(__('Number'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('country.name')
                    ->label(__('Country'))
                    ->placeholder('-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('governorate.name')
                    ->label(__('governorate'))
                    ->placeholder('-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('city.name')
                    ->label(__('City'))
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('tracking_number')
                    ->copyable()
                    ->placeholder('-')
                    ->label(__('Tracking Number'))
                    ->searchable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('shippingType.name')
                    ->label(__('Shipping Type'))
                    ->searchable()
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label(__('Payment Method'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('coupon.id')
                    ->label(__('Coupon ID'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('shipping_cost')
                    ->label(__('Shipping Cost'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tax_percentage')
                    ->label(__('Tax Percentage'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tax_amount')
                    ->label(__('Tax Amount'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label(__('Subtotal'))
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->summarize(Sum::make())
                    ->label(__('Total'))
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->multiple()
                    ->options(
                        collect(OrderStatus::cases())
                            ->mapWithKeys(fn ($status) => [$status->value => $status->getLabel()])
                            ->toArray()
                    ),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('filters.created_from')),
                        DatePicker::make('created_until')
                            ->label(__('filters.created_until')),
                    ])
                    ->query(function (\Illuminate\Contracts\Database\Eloquent\Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = __('filters.indicator_from', ['date' => Carbon::parse($data['created_from'])->toFormattedDateString()]);
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = __('filters.indicator_until', ['date' => Carbon::parse($data['created_until'])->toFormattedDateString()]);
                        }

                        return $indicators;
                    }),
            ], Tables\Enums\FiltersLayout::Modal)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(fn ($record) => in_array($record->status, [OrderStatus::Pending, OrderStatus::Preparing])),
                    Tables\Actions\DeleteAction::make(),
                ])->label(__('Actions'))
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                Step::make(__('Order Items'))
                    ->schema([
                        Repeater::make('items')
                            ->label(__('Order Items'))
                            ->relationship('items')
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('Product'))
                                    ->options(Product::pluck('name', 'id'))
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                    $set('price_per_unit', (float) Product::find($state)?->discount_price_for_current_country ?? 0)
                                    )->disabled(fn ($record, $operation) => $operation === 'edit'
                                        && $record?->bundle_id !== null)
                                ,

                                Select::make('color_id')
                                    ->label(__('Color'))
                                    ->options(fn (Get $get) => ProductColor::where('product_id', $get('product_id'))
                                        ->with('color')
                                        ->get()
                                        ->pluck('color.name', 'color.id'))
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('size_id', null))
                                    ->visible(fn (Get $get) => Product::find($get('product_id'))?->productColors()->exists()),

                                Select::make('size_id')
                                    ->label(__('Size'))
                                    ->options(fn (Get $get) => ProductColor::where([
                                        ['product_id', $get('product_id')],
                                        ['color_id', $get('color_id')],
                                    ])
                                        ->with('sizes')
                                        ->first()?->sizes
                                        ->pluck('name', 'id') ?? [])
                                    ->reactive()
                                    ->visible(fn (Get $get) => Product::find($get('product_id'))?->productColors()->exists()),

                                TextInput::make('quantity')
                                    ->required()
                                    ->label(__('Quantity'))
                                    ->numeric()
                                    ->minValue(1)
                                    ->live()
                                    ->disabled(fn ($record, $operation) => $operation === 'edit'
                                        && $record?->bundle_id !== null)
                                    ->afterStateUpdated(fn ($state, callable $set, Get $get) =>
                                    $set('subtotal', ($get('price_per_unit') ?? 0) * ($state ?? 1))
                                    ),

                                TextInput::make('price_per_unit')
                                    ->default(fn (Get $get) => (float) Product::find($get('product_id'))?->discount_price_for_current_country ?? 0)
                                    ->readOnly()
                                    ->label(__('Price per Unit'))
                                    ->numeric(),

                                TextInput::make('subtotal')
                                    ->readOnly()
                                    ->label(__('Subtotal'))
                                    ->numeric(),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->afterStateUpdated(function (Get $get, Forms\Set $set) {
                                $items = $get('items') ?? [];
                                $subtotal = collect($items)
                                    ->filter(fn ($item) => !isset($item['bundle_id']) || empty($item['bundle_id']))
                                    ->sum(fn ($item) => ($item['subtotal'] ?? 0));

                                if ($get('bundle_id')) {
                                    $subtotal += Bundle::find($get('bundle_id'))?->discount_price ?? 0;
                                }

                                $taxPercentage = Setting::getTaxPercentage();
                                $taxAmount = ($subtotal * $taxPercentage) / 100;
                                $shippingCost = $get('shipping_cost') ?? 0;
                                $total = $subtotal + $taxAmount + $shippingCost;

                                $set('subtotal', $subtotal);
                                $set('tax_amount', $taxAmount);
                                $set('total', $total);
                                $set('shipping_type_id', null);
                            }),
                    ]),

                Step::make(__('Shipping Information'))
                    ->schema([
                        Select::make('shipping_type_id')
                            ->relationship('shippingType', 'name')
                            ->required()
                            ->label(__('Shipping Type'))
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, Get $get) =>
                            self::updateShippingCost($set, $state, $get('items'), $get('city_id'), $get('governorate_id'), $get('country_id'))
                            ),

                        Select::make('country_id')
                            ->required()
                            ->label(__('Country'))
                            ->options(Country::pluck('name', 'id'))
                            ->live()
                            ->afterStateUpdated(function (callable $set, Get $get) {
                                $set('governorate_id', null);
                                $set('city_id', null);
                                self::updateShippingCost($set, $get('shipping_type_id'), $get('items'), null, null, $get('country_id'));

                                // Recalculate total when shipping details change
                                self::recalculateTotal($set, $get);
                            }),

                        Select::make('governorate_id')
                            ->required()
                            ->label(__('Governorate'))
                            ->options(function (Get $get) {
                                return Governorate::where('country_id', $get('country_id'))->pluck('name', 'id');
                            })
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                self::updateShippingCost($set, $get('shipping_type_id'), $get('items'), null, $state, $get('country_id'));
                                self::recalculateTotal($set, $get);
                            }),

                        Select::make('city_id')
                            ->label(__('City'))
                            ->options(function (Get $get) {
                                return City::where('governorate_id', $get('governorate_id'))->pluck('name', 'id');
                            })
                            ->live()
                            ->placeholder(function (Get $get) {
                                return empty($get('governorate_id')) ? __('Select a governorate first') : 'Select a city';
                            })
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                self::updateShippingCost($set, $get('shipping_type_id'), $get('items'), $state, $get('governorate_id'), $get('country_id'));
                                self::recalculateTotal($set, $get);
                            }),

                        Forms\Components\TextInput::make('shipping_cost')
                            ->columnSpanFull()
                            ->numeric()
                            ->readOnly()
                            ->label(__('Shipping Cost'))
                            ->afterStateUpdated(function ($state, Forms\Set $set, Get $get) {
                                self::recalculateTotal($set, $get);
                            }),
                    ])->columns(2),
                // **Moved Billing Information to a separate step**
                Step::make(__('Billing Information'))
                    ->schema([
                        Select::make('payment_method_id')
                            ->relationship('paymentMethod', 'name')
                            ->required()
                            ->label(__('Payment Method')),

                        Select::make('coupon_id')
                            ->relationship('coupon', 'id')
                            ->label(__('Coupon')),

                        Forms\Components\TextInput::make('tax_percentage')
                            ->required()
                            ->numeric()
                            ->readOnly()
                            ->default(self::getTaxPercentage())
                            ->live()
                            ->label(__('Tax Percentage')),

                        Forms\Components\TextInput::make('tax_amount')
                            ->live()
                            ->readOnly()
                            ->numeric()
                            ->label(__('Tax Amount')),

                        Forms\Components\TextInput::make('subtotal')
                            ->live()
                            ->numeric()
                            ->readOnly()
                            ->label(__('Subtotal')),

                        Forms\Components\TextInput::make('total')
                            ->live()
                            ->numeric()
                            ->readOnly()
                            ->label(__('Total')),
                    ])->columns(2),

                Step::make(__('Order Details'))
                    ->schema([
                        Forms\Components\MarkdownEditor::make('notes')
                            ->label(__('Notes'))
                            ->columnSpan('full'),
                    ]),


            ])->columnSpanFull()->skippable()
        ]);
    }

    private static function calculateProductShippingCost(Product $product, $cityId, $governorateId, $countryId): float
    {
        if ($product->is_free_shipping) {
            return 0.0;
        }

        // If shipping locations are disabled, force cost to 0
        if (!Setting::isShippingLocationsEnabled()) {
            return 0.0;
        }

        if (!$cityId && !$governorateId && !$countryId) {
            return $product->cost ?? 0.0;
        }

        return self::getProductShippingCost($product, $cityId, $governorateId, $countryId)
            ?? self::getLocationBasedShippingCost($cityId, $governorateId, $countryId)
            ?? $product->cost
            ?? 0.0;
    }

    private static function updateShippingCost(callable $set, $shippingTypeId, $items, $cityId, $governorateId, $countryId): void
    {
        $totalShippingCost = 0.0;
        $hasChargeableItems = false;

        // Check if all items are free shipping
        foreach ($items as $item) {
            $product = Product::find($item['product_id'] ?? null);
            if ($product && !$product->is_free_shipping) {
                $hasChargeableItems = true;
                break;
            }
        }

        // If all products have free shipping, set cost to 0 and return
        if (!$hasChargeableItems) {
            $set('shipping_cost', 0.0);
            return;
        }

        // If shipping locations are disabled, force cost to 0
        if (!Setting::isShippingLocationsEnabled()) {
            $set('shipping_cost', 0.0);
            return;
        }

        // Add shipping type cost normally (only if there are chargeable items)
        if ($shippingTypeId) {
            $shippingType = ShippingType::find($shippingTypeId);
            $totalShippingCost += $shippingType?->shipping_cost ?? 0.0;
        }

        // Get the highest shipping cost per item instead of summing them
        $highestShippingCost = 0.0;
        if (!empty($items) && ($cityId || $governorateId || $countryId)) {
            foreach ($items as $item) {
                $product = Product::find($item['product_id'] ?? null);
                if ($product && !$product->is_free_shipping) {
                    $cost = self::calculateProductShippingCost($product, $cityId, $governorateId, $countryId);
                    if ($cost > $highestShippingCost) {
                        $highestShippingCost = $cost;
                    }
                }
            }
        }

        // Set the final shipping cost (highest among products)
        $set('shipping_cost', $totalShippingCost + $highestShippingCost);
    }

    private static function recalculateTotal(callable $set, Get $get): void
    {
        $items = collect($get('items') ?? []);
        $subtotal = $items->sum(fn ($item) => $item['subtotal'] ?? 0);

        if ($bundleId = $get('bundle_id')) {
            $subtotal += Bundle::find($bundleId)?->discount_price ?? 0;
        }

        $taxPercentage = self::getTaxPercentage();
        $taxAmount = ($subtotal * $taxPercentage) / 100;
        $shippingCost = $get('shipping_cost') ?? 0;
        $total = $subtotal + $taxAmount + $shippingCost;

        $set('subtotal', $subtotal);
        $set('tax_amount', $taxAmount);
        $set('total', $total);
    }

    private static function getProductShippingCost(Product $product, $cityId, $governorateId, $countryId): ?float
    {
        // If shipping locations are disabled, return 0 immediately
        if (!Setting::isShippingLocationsEnabled()) {
            return 0.0;
        }

        if ($cityId) {
            $cost = $product->shippingCosts()
                ->where('city_id', $cityId)
                ->where('country_id', $countryId)
                ->value('cost');
            if ($cost !== null) return $cost;
        }

        if ($governorateId) {
            $cost = $product->shippingCosts()
                ->where('governorate_id', $governorateId)
                ->where('country_id', $countryId)
                ->whereNull('city_id')
                ->value('cost');
            if ($cost !== null) return $cost;
        }

        if ($countryId) {
            return $product->shippingCosts()
                ->where('country_id', $countryId)
                ->whereNull('city_id')
                ->whereNull('governorate_id')
                ->value('cost');
        }

        return null;
    }

    private static function getLocationBasedShippingCost($cityId, $governorateId, $countryId): ?float
    {
        // If shipping locations are disabled, return 0 immediately
        if (!Setting::isShippingLocationsEnabled()) {
            return 0.0;
        }

        if ($cityId) {
            $city = City::find($cityId);
            if ($city?->cost > 0) return (float) $city->cost;
        }

        if ($governorateId) {
            $governorate = Governorate::find($governorateId);
            if ($governorate?->cost > 0) return (float) $governorate->cost;

            $zone = $governorate->shippingZones()->first();
            if ($zone?->cost > 0) return (float) $zone->cost;
        }

        if ($countryId) {
            $country = Country::find($countryId);
            if ($country?->cost > 0) return (float) $country->cost;
        }

        return null;
    }

    private static function getTaxPercentage(): float
    {
        return Setting::getTaxPercentage() ?? 0;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
            'view' => ViewOrder::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
