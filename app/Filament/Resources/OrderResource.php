<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingType;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    use Translatable;

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label(__('user_id')),

                Forms\Components\Select::make('contact_id')
                    ->relationship('contact', 'name')
                    ->label(__('contact_id')),

                Forms\Components\Select::make('shipping_type_id')
                    ->relationship('shippingType', 'name')
                    ->required()
                    ->label(__('shipping_type_id'))
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('shipping_cost', self::calculateShippingCost($state))),

                Forms\Components\Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->required()
                    ->label(__('payment_method_id')),

                Forms\Components\Select::make('coupon_id')
                    ->relationship('coupon', 'id')
                    ->label(__('coupon_id')),

                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->label(__('country_id'))
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('shipping_cost', self::calculateShippingCost($state))),

                Forms\Components\Select::make('governorate_id')
                    ->relationship('governorate', 'name')
                    ->label(__('governorate_id'))
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('shipping_cost', self::calculateShippingCost($state))),

                Forms\Components\Select::make('city_id')
                    ->relationship('city', 'name')
                    ->label(__('city_id'))
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('shipping_cost', self::calculateShippingCost($state))),

                Forms\Components\TextInput::make('shipping_cost')
                    ->numeric()
                    ->disabled()
                    ->label(__('shipping_cost')),

                Forms\Components\TextInput::make('tax_percentage')
                    ->numeric()
                    ->default(self::getTaxPercentage())
                    ->disabled()
                    ->label(__('tax_percentage')),

                Forms\Components\TextInput::make('tax_amount')
                    ->numeric()
                    ->disabled()
                    ->label(__('tax_amount')),

                Forms\Components\TextInput::make('subtotal')
                    ->numeric()
                    ->disabled()
                    ->label(__('subtotal')),

                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->disabled()
                    ->label(__('total')),

                Forms\Components\TextInput::make('status')
                    ->required()
                    ->label(__('status')),

                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull()
                    ->label(__('notes')),
            ]);
    }

    private static function calculateShippingCost($shippingTypeId): float
    {
        if (!$shippingTypeId) {
            return 0.0;
        }

        $shippingType = ShippingType::find($shippingTypeId);
        return $shippingType ? $shippingType->cost : 0.0;
    }

    private static function getTaxPercentage(): float
    {
        return Setting::first()?->tax_percentage ?? 0.0;
    }

    private static function getLocationBasedShippingCost($cityId, $governorateId, $countryId): float
    {
        if ($cityId) {
            $city = City::find($cityId);
            if ($city && $city->cost !== null) {
                return $city->cost;
            }
        }

        if ($governorateId) {
            $governorate = Governorate::find($governorateId);
            if ($governorate && $governorate->cost !== null) {
                return $governorate->cost;
            }
        }

        if ($countryId) {
            $country = Country::find($countryId);
            if ($country && $country->cost !== null) {
                return $country->cost;
            }
        }

        return 0.0;
    }

    private static function getProductShippingCost(Product $product, $cityId, $governorateId, $countryId): float
    {
        $shippingCosts = $product->shippingCosts()->get();

        if ($shippingCosts->where('city_id', $cityId)->isNotEmpty()) {
            return $shippingCosts->where('city_id', $cityId)->first()->cost;
        }

        if ($shippingCosts->where('governorate_id', $governorateId)->isNotEmpty()) {
            return $shippingCosts->where('governorate_id', $governorateId)->first()->cost;
        }

        if ($shippingCosts->where('country_id', $countryId)->isNotEmpty()) {
            return $shippingCosts->where('country_id', $countryId)->first()->cost;
        }

        return $product->cost ?? 0.0;
    }

    private static function calculateTotals($subtotal, $shippingCost): array
    {
        $taxPercentage = self::getTaxPercentage();
        $taxAmount = ($subtotal * $taxPercentage) / 100;
        $total = $subtotal + $shippingCost + $taxAmount;

        return [
            'tax_amount' => $taxAmount,
            'total' => $total,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->placeholder('-')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact.name')
                    ->searchable()
                    ->placeholder('-')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                ->searchable(),
                Tables\Columns\TextColumn::make('shippingType.name')
                    ->searchable()
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->searchable()
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('coupon.id')
                    ->searchable()
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_cost')
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax_percentage')
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax_amount')
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
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
                            $indicators['created_from'] = 'Order from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Order until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ], Tables\Enums\FiltersLayout::Modal)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    ...collect(OrderStatus::cases())->map(fn ($status) =>
                    Tables\Actions\Action::make($status->value)
                        ->label(__($status->getLabel()))
                        ->icon($status->getIcon())
                        ->color($status->getColor())
                        ->action(fn ($record) => $record->update(['status' => $status->value]))
                    )->toArray(),

                    Tables\Actions\DeleteAction::make(),
                ])->label(__('Actions'))
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ...collect(OrderStatus::cases())->map(fn ($status) =>
                    Tables\Actions\BulkAction::make($status->value)
                        ->label(__($status->getLabel()))
                        ->icon($status->getIcon())
                        ->color($status->getColor())
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['status' => $status->value])))
                    )->toArray(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
