<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Mail\OrderConfirmationMail;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingType;
use App\Services\JtExpressService;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class OrderResource extends Resource
{
    use Translatable;

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

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
                        ->action(fn ($record) => self::updateOrderStatus($record, $status))
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

    public static function updateOrderStatus($order, OrderStatus $status)
    {
        $order->update(['status' => $status->value]);

        Mail::to($order->user->email ?? $order->contact->email)->queue(new OrderConfirmationMail($order));

        // Trigger JT Express only when the status is set to "Confirmed"
        if ($status === OrderStatus::Shipping) {
            $JtExpressOrderData = self::prepareJtExpressOrderData($order);
            $jtExpressResponse = app(JtExpressService::class)->createOrder($JtExpressOrderData);
            self::updateJtExpressOrder($order, 'pending', $JtExpressOrderData, $jtExpressResponse);
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->hidden(fn (Get $get) => $get('contact_id'))
                    ->relationship('user', 'name')
                    ->label(__('user_id')),

                Select::make('contact_id')
                    ->hidden(fn (Get $get) => $get('user_id'))
                    ->relationship('contact', 'name')
                    ->label(__('contact_id')),

                Select::make('shipping_type_id')
                    ->relationship('shippingType', 'name')
                    ->required()
                    ->label(__('shipping_type_id'))
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, Get $get) =>
                    $set('shipping_cost', self::calculateShippingCost($state, $get('city_id'), $get('governorate_id'), $get('country_id')))
                    ),

                Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->required()
                    ->label(__('payment_method_id')),

                Select::make('coupon_id')
                    ->relationship('coupon', 'id')
                    ->label(__('coupon_id')),

                Select::make('country_id')
                    ->label(__('country_id'))
                    ->options(Country::pluck('name', 'id'))
                    ->live()
                    ->afterStateUpdated(fn (callable $set) =>
                    $set('governorate_id', null)
                        ->set('city_id', null)
                    ),

                Select::make('governorate_id')
                    ->label(__('governorate_id'))
                    ->options(fn (Get $get) => Governorate::where('country_id', $get('country_id'))->pluck('name', 'id'))
                    ->live()
                    ->placeholder(fn (Get $get) => empty($get('country_id')) ? 'Select a country first' : 'Select a governorate')
                    ->afterStateUpdated(fn ($state, callable $set, Get $get) =>
                    $set('city_id', null)
                    ),

                Select::make('city_id')
                    ->label(__('city_id'))
                    ->options(fn (Get $get) => City::where('governorate_id', $get('governorate_id'))->pluck('name', 'id'))
                    ->live()
                    ->placeholder(fn (Get $get) => empty($get('governorate_id')) ? 'Select a governorate first' : 'Select a city'),

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

    private static function calculateShippingCost($shippingTypeId, $cityId = null, $governorateId = null, $countryId = null): float
    {
        $shippingCost = 0.0;

        if ($shippingTypeId) {
            $shippingType = ShippingType::find($shippingTypeId);
            $shippingCost += $shippingType?->shipping_cost ?? 0.0;
        }

        $locationCost = self::getLocationBasedShippingCost($cityId, $governorateId, $countryId);
        return $shippingCost + $locationCost;
    }

    private static function getLocationBasedShippingCost($cityId, $governorateId, $countryId): float
    {
        if ($cityId) {
            $city = City::find($cityId);
            if ($city && $city->cost !== null) {
                return (float) $city->cost;
            }
        }

        if ($governorateId) {
            $governorate = Governorate::find($governorateId);
            if ($governorate && $governorate->cost !== null) {
                return (float) $governorate->cost;
            }
        }

        if ($countryId) {
            $country = Country::find($countryId);
            if ($country && $country->cost !== null) {
                return (float) $country->cost;
            }
        }

        return 0.0;
    }

    private static function getTaxPercentage(): float
    {
        return Setting::first()?->tax_percentage ?? 0.0;
    }

    private static function getProductShippingCost(Product $product, $cityId, $governorateId, $countryId): float
    {
        $shippingCosts = $product->shippingCosts()->get();

        if ($shippingCosts->where('city_id', $cityId)->isNotEmpty()) {
            return (float) $shippingCosts->where('city_id', $cityId)->first()->cost;
        }

        if ($shippingCosts->where('governorate_id', $governorateId)->isNotEmpty()) {
            return (float) $shippingCosts->where('governorate_id', $governorateId)->first()->cost;
        }

        if ($shippingCosts->where('country_id', $countryId)->isNotEmpty()) {
            return (float) $shippingCosts->where('country_id', $countryId)->first()->cost;
        }

        return (float) ($product->cost ?? 0.0);
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

    private static function prepareJtExpressOrderData($order): array
    {
        $data = [
            'tracking_number'   => '#'. $order->id. ' EGY' . time() . rand(1000, 9999),
            'weight'            => 1.0, // You might want to calculate the total weight dynamically
            'quantity'          => $order->items->sum('quantity'), // Sum of all item quantities in the order

            'remark'            => implode(' , ', array_filter([
                'Notes: ' . ($order->notes ?? 'No notes'),
                $order->user?->name ? 'User: ' . $order->user->name : null,
                $order->user?->email ? 'Email: ' . $order->user->email : null,
                $order->user?->phone ? 'Phone: ' . $order->user->phone : null,
                $order->user?->address ? 'Address: ' . $order->user->address : null,
                $order->contact?->name ? 'Contact: ' . $order->contact->name : null,
                $order->contact?->email ? 'Contact Email: ' . $order->contact->email : null,
                $order->contact?->phone ? 'Contact Phone: ' . $order->contact->phone : null,
                $order->contact?->address ? 'Contact Address: ' . $order->contact->address : null,
            ])),

            'item_name'         => $order->items->pluck('product.name')->implode(', '), // Concatenated product names
            'item_quantity'     => $order->items->count(), // Total distinct items in the order
            'item_value'        => $order->total, // Order total amount
            'item_currency'     => 'EGP',
            'item_description'  => $order->notes ?? 'No description provided',
        ];

        $data['sender'] = [
            'name'                   => 'Your Company Name',
            'company'                => 'Your Company',
            'city'                   => 'Your City',
            'address'                => 'Your Full Address',
            'mobile'                 => 'Your Contact Number',
            'countryCode'            => 'Your Country Code',
            'prov'                   => 'Your Prov',
            'area'                   => 'Your Area',
            'town'                   => 'Your Town',
            'street'                 => 'Your Street',
            'addressBak'             => 'Your Address Bak',
            'postCode'               => 'Your Post Code',
            'phone'                  => 'Your Phone',
            'mailBox'                => 'Your Mail Box',
            'areaCode'               => 'Your Area Code',
            'building'               => 'Your Building',
            'floor'                  => 'Your Floor',
            'flats'                  => 'Your Flats',
            'alternateSenderPhoneNo' => 'Your Alternate Sender Phone No',
        ];

        $data['receiver'] = [
            'name'                      => 'test', // $order->name,
            'prov'                      => 'أسيوط', // $order->region->governorate->name,
            'city'                      => 'القوصية', // $order->region->name,
            'address'                   => 'sdfsacdscdscdsa', // $order->address,
            'mobile'                    => '1441234567', // $order->phone,
            'company'                   => 'guangdongshengshenzhe',
            'countryCode'               => 'EGY',
            'area'                      => 'الصبحه',
            'town'                      => 'town',
            'addressBak'                => 'receivercdsfsafdsaf lkhdlksjlkfjkndskjfnhskjlkafdslkjdshflksjal',
            'street'                    => 'street',
            'postCode'                  => '54830',
            'phone'                     => '23423423423',
            'mailBox'                   => 'ant_li123@qq.com',
            'areaCode'                  => '2342343',
            'building'                  => '13',
            'floor'                     => '25',
            'flats'                     => '47',
            'alternateReceiverPhoneNo'  => $order->another_phone ?? '1231321322',
        ];

        return $data;
    }

    private static function updateJtExpressOrder(Order $order, string $shipping_status, $JtExpressOrderData, $jtExpressResponse)
    {
        if (isset($jtExpressResponse['code']) && $jtExpressResponse['code'] == 1) {
            $order->update([
                'tracking_number'   => $JtExpressOrderData['tracking_number'],
                'shipping_status'   => $shipping_status,
                'shipping_response' => json_encode($jtExpressResponse)
            ]);
        }
    }
}
