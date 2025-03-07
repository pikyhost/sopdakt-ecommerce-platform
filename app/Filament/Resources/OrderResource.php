<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Mail\OrderConfirmationMail;
use App\Models\Bundle;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\LandingPageOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductColorSize;
use App\Models\Setting;
use App\Models\ShippingType;
use App\Services\JtExpressService;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class OrderResource extends Resource
{
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


                    Tables\Actions\Action::make('trackOrder')
                        ->visible(fn (Order $record): bool =>
                        !is_null($record->tracking_number)
                        )
                        ->label('Track Order')
                        ->icon('heroicon-o-map')
                        ->color('success')
                        ->visible(fn (Order $record): bool =>
                            !is_null($record->tracking_number) &&
                            !is_null($record->shipping_status)
                        )
                        ->action(function (Order $record): void {
                            $shipping_response = json_decode($record->shipping_response);
                            $trackingInfo = app(JtExpressService::class)->trackLogistics($shipping_response->data);

                            if (isset($trackingInfo['success']) && $trackingInfo['success']) {
                                Notification::make()
                                    ->title('Tracking Information')
                                    ->body('Tracking details retrieved successfully: ' . ($trackingInfo['data']['billCode'] ?? $record->tracking_number))
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Tracking Error')
                                    ->body($trackingInfo['msg'] ?? 'Unable to retrieve tracking information')
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('checkOrder')
                        ->visible(fn (Order $record): bool =>
                        !is_null($record->tracking_number)
                        )
                        ->label('Check Order Status')
                        ->icon('heroicon-o-check-circle')
                        ->color('primary')
                        ->visible(fn (Order $record): bool =>
                            !is_null($record->tracking_number)
                        )
                        ->action(function (Order $record): void {
                            $shipping_response = json_decode($record->shipping_response);
                            $orderInfo = app(JtExpressService::class)->checkingOrder($shipping_response->data);

                            if (isset($orderInfo['success']) && $orderInfo['success']) {
                                Notification::make()
                                    ->title('Order Status')
                                    ->body('Order exists: ' . ($orderInfo['data']['isExist'] ?? 'Unknown'))
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Check Error')
                                    ->body($orderInfo['msg'] ?? 'Unable to check order status')
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('getOrderStatus')
                        ->visible(fn (Order $record): bool =>
                        !is_null($record->tracking_number)
                        )
                        ->label('Get Detailed Status')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->color('info')
                        ->visible(fn (Order $record): bool =>
                            !is_null($record->tracking_number)
                        )
                        ->action(function (Order $record): void {
                            $shipping_response = json_decode($record->shipping_response);
                            $statusInfo = app(JtExpressService::class)->getOrderStatus($shipping_response->data);

                            if (isset($statusInfo['success']) && $statusInfo['success']) {
                                $status = $statusInfo['data']['deliveryStatus'] ?? 'Unknown';

                                $record->update([
                                    'shipping_status' => $status,
                                    'shipping_response' => json_encode($statusInfo)
                                ]);

                                Notification::make()
                                    ->title('Order Status Updated')
                                    ->body('Current status: ' . $status)
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Status Error')
                                    ->body($statusInfo['msg'] ?? 'Unable to get order status')
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('getTrajectory')
                        ->visible(fn (Order $record): bool =>
                        !is_null($record->tracking_number)
                        )
                        ->label('View Delivery Trajectory')
                        ->icon('heroicon-o-arrow-path')
                        ->color('gray')
                        ->visible(fn (Order $record): bool =>
                            !is_null($record->tracking_number)
                        )
                        ->action(function (Order $record): void {
                            $shipping_response = json_decode($record->shipping_response);
                            $trajectoryInfo = app(JtExpressService::class)->getLogisticsTrajectory($shipping_response->data);

                            if (isset($trajectoryInfo['success']) && $trajectoryInfo['success']) {
                                $steps = count($trajectoryInfo['data']['details'] ?? []);

                                Notification::make()
                                    ->title('Delivery Trajectory')
                                    ->body("Retrieved {$steps} tracking events for this shipment.")
                                    ->success()
                                    ->send();

                                // You could store this information or display it in a modal
                            } else {
                                Notification::make()
                                    ->title('Trajectory Error')
                                    ->body($trajectoryInfo['msg'] ?? 'Unable to get trajectory information')
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('cancelOrder')
                        ->visible(fn (Order $record): bool =>
                        !is_null($record->tracking_number)
                        )
                        ->label('Cancel Order')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (Order $record): bool =>
                            !is_null($record->tracking_number) &&
                            $record->shipping_status !== 'delivered' &&
                            $record->shipping_status !== 'cancelled'
                        )
                        ->requiresConfirmation()
                        ->action(function (Order $record): void {
                            $shipping_response = json_decode($record->shipping_response);
                            $cancelResult = app(JtExpressService::class)->cancelOrder($shipping_response->data);

                            if (isset($cancelResult['success']) && $cancelResult['success']) {
                                $record->update([
                                    'shipping_status' => 'cancelled',
                                    'shipping_response' => json_encode($cancelResult)
                                ]);

                                Notification::make()
                                    ->title('Order Cancelled')
                                    ->body('The order has been successfully cancelled.')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Cancellation Error')
                                    ->body($cancelResult['msg'] ?? 'Unable to cancel the order')
                                    ->danger()
                                    ->send();
                            }
                        }),
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

        // Trigger JT Express only when the status is set to "Confirmed"
        if ($status === OrderStatus::Shipping) {
            $JtExpressOrderData = self::prepareJtExpressOrderData($order);
            $jtExpressResponse = app(JtExpressService::class)->createOrder($JtExpressOrderData);
            self::updateJtExpressOrder($order, 'pending', $JtExpressOrderData, $jtExpressResponse);
            Mail::to($order->user->email ?? $order->contact->email)->queue(new OrderConfirmationMail($order));
        }
    }


    private static function prepareJtExpressOrderData($order): array
    {
        $data = [
            'tracking_number'   => '#'. $order->id. ' EGY' . time() . rand(1000, 9999),
            'weight'            => 1.0, // You might want to calculate the total weight dynamically
            'quantity'          => 1,  // $order->items->sum('quantity') Sum of all item quantities in the order

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
                'tracking_number'   => $JtExpressOrderData['tracking_number'] ?? null,
                'shipping_status'   => $shipping_status,
                'shipping_response' => json_encode($jtExpressResponse)
            ]);
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
        return $form->schema([
            Wizard::make([
                Step::make('Order Details')
                    ->schema([
                        Select::make('user_id')
                            ->live()
                            ->hidden(fn (Get $get) => $get('contact_id'))
                            ->relationship('user', 'name')
                            ->label(__('Customer')),

                        Select::make('contact_id')
                            ->live()
                            ->hidden(fn (Get $get) => $get('user_id'))
                            ->relationship('contact', 'name')
                            ->label(__('Contact')),

                        Forms\Components\ToggleButtons::make('status')
                            ->label(__('Status'))
                            ->inline()
                            ->options(OrderStatus::class)
                            ->required(),

                        Forms\Components\MarkdownEditor::make('notes')
                            ->label(__('Notes'))
                            ->columnSpan('full'),
                    ]),

                Step::make('Shipping Information')
                    ->schema([
                        Select::make('shipping_type_id')
                            ->relationship('shippingType', 'name')
                            ->required()
                            ->label(__('Shipping Type'))
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, Get $get) =>
                            self::updateShippingCost($set, $state, $get('city_id'), $get('governorate_id'), $get('country_id'))
                            ),

                        Select::make('country_id')
                            ->label(__('Country'))
                            ->options(Country::pluck('name', 'id'))
                            ->live()
                            ->afterStateUpdated(function (callable $set, Get $get) {
                                $set('governorate_id', null);
                                $set('city_id', null);
                                self::updateShippingCost($set, $get('shipping_type_id'), null, null, $get('country_id'));
                            }),

                        Select::make('governorate_id')
                            ->label(__('Governorate'))
                            ->options(fn (Get $get) => Governorate::where('country_id', $get('country_id'))->pluck('name', 'id'))
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, Get $get) =>
                            self::updateShippingCost($set, $get('shipping_type_id'), null, $state, $get('country_id'))
                            ),

                        Select::make('city_id')
                            ->label(__('City'))
                            ->options(fn (Get $get) => City::where('governorate_id', $get('governorate_id'))->pluck('name', 'id'))
                            ->live()
                            ->placeholder(fn (Get $get) => empty($get('governorate_id')) ? 'Select a governorate first' : 'Select a city')
                            ->afterStateUpdated(fn ($state, callable $set, Get $get) =>
                            self::updateShippingCost($set, $get('shipping_type_id'), $state, $get('governorate_id'), $get('country_id'))
                            ),

                        Forms\Components\TextInput::make('shipping_cost')
                            ->numeric()
                            ->disabled()
                            ->label(__('shipping_cost')),
                    ]),

                Step::make('Order Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('Product'))
                                    ->options(Product::pluck('name', 'id'))
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                    $set('price_per_unit', Product::find($state)?->price ?? '')
                                    ),

                                TextInput::make('quantity')
                                    ->label(__('Quantity'))
                                    ->numeric()
                                    ->minValue(1)
                                    ->live()
                                    ->afterStateUpdated(fn ($state, callable $set, Get $get) =>
                                    $set('subtotal', ($get('price_per_unit') ?? 0) * ($state ?? 1))
                                    ),

                                TextInput::make('price_per_unit')
                                    ->default(fn (Get $get) => Product::find($get('../product_id'))?->id ?? 0) // Get price based on product_id
                                    ->readOnly()
                                    ->label(__('Price per Unit'))
                                    ->numeric(),

                                TextInput::make('subtotal')
                                    ->readOnly()
                                    ->label(__('Subtotal'))
                                    ->numeric(),
                            ])
                            ->columnSpanFull()
                            ->collapsible(),
                    ]),

                // **Moved Billing Information to a separate step**
                Step::make('Billing Information')
                    ->schema([
                        Select::make('payment_method_id')
                            ->columnSpanFull()
                            ->relationship('paymentMethod', 'name')
                            ->required()
                            ->label(__('Payment Method')),

                        Select::make('coupon_id')
                            ->columnSpanFull()
                            ->relationship('coupon', 'id')
                            ->label(__('Coupon')),

                        Forms\Components\TextInput::make('tax_percentage')
                            ->required()
                            ->numeric()
                            ->default(self::getTaxPercentage())
                            ->live()
                            ->afterStateUpdated(function (Get $get, Forms\Set $set) {
                                $subtotal = collect($get('items'))->sum(fn ($item) => $item['subtotal'] ?? 0);
                                $taxPercentage = $get('tax_percentage') ?? 0;
                                $taxAmount = ($subtotal * $taxPercentage) / 100;
                                $shippingCost = $get('shipping_cost') ?? 0;
                                $total = $subtotal + $taxAmount + $shippingCost;

                                $set('subtotal', $subtotal);
                                $set('tax_amount', $taxAmount);
                                $set('total', $total);
                            })
                            ->label(__('Tax Percentage')),

                        Forms\Components\TextInput::make('tax_amount')
                            ->numeric()
                            ->disabled()
                            ->label(__('Tax Amount')),

                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->disabled()
                            ->label(__('Subtotal')),

                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->disabled()
                            ->label(__('Total')),
                    ])->columns(2),
            ])->columnSpanFull()
        ]);
    }


    public static function form2(Form $form): Form
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

                Forms\Components\TextInput::make('status')
                    ->required()
                    ->label(__('status')),

                Select::make('shipping_type_id')
                    ->relationship('shippingType', 'name')
                    ->required()
                    ->label(__('shipping_type_id'))
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, Get $get) =>
                    self::updateShippingCost($set, $state, $get('city_id'), $get('governorate_id'), $get('country_id'))
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
                    ->afterStateUpdated(function (callable $set, Get $get) {
                        $set('governorate_id', null);
                        $set('city_id', null);
                        self::updateShippingCost($set, $get('shipping_type_id'), null, null, $get('country_id'));
                    }),

                Select::make('governorate_id')
                    ->label(__('governorate_id'))
                    ->options(fn (Get $get) => Governorate::where('country_id', $get('country_id'))->pluck('name', 'id'))
                    ->live()
                    ->placeholder(fn (Get $get) => empty($get('country_id')) ? 'Select a country first' : 'Select a governorate')
                    ->afterStateUpdated(fn ($state, callable $set, Get $get) =>
                    self::updateShippingCost($set, $get('shipping_type_id'), null, $state, $get('country_id'))
                    ),

                Select::make('city_id')
                    ->label(__('city_id'))
                    ->options(fn (Get $get) => City::where('governorate_id', $get('governorate_id'))->pluck('name', 'id'))
                    ->live()
                    ->placeholder(fn (Get $get) => empty($get('governorate_id')) ? 'Select a governorate first' : 'Select a city')
                    ->afterStateUpdated(fn ($state, callable $set, Get $get) =>
                    self::updateShippingCost($set, $get('shipping_type_id'), $state, $get('governorate_id'), $get('country_id'))
                    ),

                Forms\Components\TextInput::make('shipping_cost')
                    ->numeric()
                    ->disabled()
                    ->label(__('shipping_cost')),

                Forms\Components\Repeater::make('items')
                    ->relationship('items') // Defines the relationship with OrderItem model
                    ->schema([
                        Select::make('bundle_id')
                            ->label(__('Bundle'))
                            ->options(Bundle::pluck('name', 'id'))
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                            $set('price_per_unit', Bundle::find($state)?->price ?? 0)
                            ),

                        Select::make('product_id')
                            ->label(__('Product'))
                            ->options(Product::pluck('name', 'id'))
                            ->searchable()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $set('color_id', null); // Reset color when product changes
                                $set('price_per_unit', Product::find($state)?->price ?? 0); // Set price based on product
                            })
                            ->reactive(),

                        Select::make('color_id')
                            ->label(__('Color'))
                            ->options(fn (Get $get) =>
                            ProductColor::where('product_id', $get('product_id'))
                                ->with('color')
                                ->get()
                                ->pluck('color.name', 'color.id')
                            )
                            ->live()
                            ->disabled(fn (Get $get) => empty($get('product_id')))
                            ->afterStateUpdated(fn ($state, callable $set) => $set('size_id', null)), // Reset size when color changes

                        Select::make('size_id')
                            ->label(__('Size'))
                            ->options(fn (Get $get) =>
                            ProductColorSize::whereHas('productColor', function ($query) use ($get) {
                                $query->where('product_id', $get('product_id'))
                                    ->where('color_id', $get('color_id'));
                            })
                                ->with('size')
                                ->get()
                                ->pluck('size.name', 'size.id')
                            )
                            ->disabled(fn (Get $get) => empty($get('color_id'))),

                        TextInput::make('quantity')
                            ->label(__('Quantity'))
                            ->numeric()
                            ->minValue(1)
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set, Get $get) =>
                            $set('subtotal', ($get('price_per_unit') ?? 0) * ($state ?? 1))
                            ),

                        TextInput::make('price_per_unit')
                            ->default(fn (Get $get) => Product::find($get('../product_id'))?->id ?? 0) // Get price based on product_id
                            ->readOnly()
                            ->label(__('Price per Unit'))
                            ->numeric(),


                        TextInput::make('subtotal')
                            ->readOnly()
                            ->label(__('Subtotal'))
                            ->numeric(),
                    ])
                    ->columnSpanFull()
                    ->collapsible(),

                Forms\Components\TextInput::make('tax_percentage')
                    ->numeric()
                    ->default(self::getTaxPercentage())
                    ->live()
                    ->afterStateUpdated(function (Get $get, Forms\Set $set) {
                        $subtotal = collect($get('items'))
                            ->sum(fn ($item) => $item['subtotal'] ?? 0);

                        $taxPercentage = $get('tax_percentage') ?? 0;
                        $taxAmount = ($subtotal * $taxPercentage) / 100;
                        $shippingCost = $get('shipping_cost') ?? 0;
                        $total = $subtotal + $taxAmount + $shippingCost;

                        $set('subtotal', $subtotal);
                        $set('tax_amount', $taxAmount);
                        $set('total', $total);
                    })
                    ->label(__('Tax Percentage')),

                Forms\Components\TextInput::make('tax_amount')
                    ->numeric()
                    ->disabled()
                    ->label(__('Tax Amount')),

                Forms\Components\TextInput::make('subtotal')
                    ->numeric()
                    ->disabled()
                    ->label(__('Subtotal')),

                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->disabled()
                    ->label(__('Total')),

                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull()
                    ->label(__('notes')),
            ]);
    }



    private static function updateShippingCost(callable $set, $shippingTypeId, $cityId, $governorateId, $countryId): void
    {
        $shippingCost = self::calculateShippingCost($shippingTypeId, $cityId, $governorateId, $countryId);
        $set('shipping_cost', $shippingCost);
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
        return Setting::first()?->tax_percentage ?? 0;
    }
}
