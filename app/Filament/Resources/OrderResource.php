<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Mail\OrderConfirmationMail;
use App\Models\Bundle;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\Setting;
use App\Models\ShippingType;
use App\Services\JtExpressService;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
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

    public static function getNavigationGroup(): ?string
    {
        return __('landing_page_order.orders_contacts');
    }

    public static function getNavigationLabel(): string
    {
        return __('landing_page_order.orders');
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
                    ->formatStateUsing(fn($state) => '#' . $state)
                    ->label(__('Number'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('User Name'))
                    ->searchable()
                    ->placeholder('-')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contact.name')
                    ->label(__('Contact Name'))
                    ->searchable()
                    ->placeholder('-')
                    ->numeric()
                    ->sortable(),

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
                    ->searchable()
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('coupon.id')
                    ->label(__('Coupon ID'))
                    ->searchable()
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('shipping_cost')
                    ->label(__('Shipping Cost'))
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tax_percentage')
                    ->label(__('Tax Percentage'))
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tax_amount')
                    ->label(__('Tax Amount'))
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label(__('Subtotal'))
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
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
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('filters.created_from'))
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(__('filters.created_until'))
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
                    Tables\Actions\EditAction::make(),
                    ...collect(OrderStatus::cases())->map(fn ($status) =>
                    Tables\Actions\Action::make($status->value)
                        ->label(__($status->getLabel()))
                        ->icon($status->getIcon())
                        ->color($status->getColor())
                        ->action(fn ($record) => self::updateOrderStatus($record, $status))
                    )->toArray(),

                    Tables\Actions\DeleteAction::make(),

                    Tables\Actions\Action::make('trackOrder')
                        ->label(__('actions.track_order'))
                        ->visible(fn (Order $record): bool =>
                        !is_null($record->tracking_number)
                        )
                        ->icon('heroicon-o-map')
                        ->color('success')
                        ->visible(fn (Order $record): bool =>
                            !is_null($record->tracking_number) &&
                            !is_null($record->shipping_status)
                        )
                        ->action(function (Order $record): void {
                            $shipping_response = json_decode($record->shipping_response);
                            $trackingInfo = app(JtExpressService::class)->trackLogistics($shipping_response->data);

                            if (isset($trackingInfo['code']) && $trackingInfo['code'] == 1) {
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
                        ->label(__('actions.check_order'))
                        ->visible(fn (Order $record): bool =>
                        !is_null($record->tracking_number)
                        )
                        ->icon('heroicon-o-check-circle')
                        ->color('primary')
                        ->visible(fn (Order $record): bool =>
                        !is_null($record->tracking_number)
                        )
                        ->action(function (Order $record): void {
                            $shipping_response = json_decode($record->shipping_response);
                            $orderInfo = app(JtExpressService::class)->checkingOrder($shipping_response->data);

                            if (isset($orderInfo['code']) && $orderInfo['code'] == 1) {
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
                        ->label(__('actions.get_order_status'))
                        ->visible(fn (Order $record): bool =>
                        !is_null($record->tracking_number)
                        )
                        ->icon('heroicon-o-clipboard-document-list')
                        ->color('info')
                        ->visible(fn (Order $record): bool =>
                        !is_null($record->tracking_number)
                        )
                        ->action(function (Order $record): void {
                            $shipping_response = json_decode($record->shipping_response);
                            $statusInfo = app(JtExpressService::class)->getOrderStatus($shipping_response->data);

                            if (isset($statusInfo['code']) && $statusInfo['code'] == 1) {
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
                        ->label(__('actions.get_trajectory'))
                        ->visible(fn (Order $record): bool =>
                        !is_null($record->tracking_number)
                        )
                        ->icon('heroicon-o-arrow-path')
                        ->color('gray')
                        ->visible(fn (Order $record): bool =>
                        !is_null($record->tracking_number)
                        )
                        ->action(function (Order $record): void {
                            $shipping_response = json_decode($record->shipping_response);
                            $trajectoryInfo = app(JtExpressService::class)->getLogisticsTrajectory($shipping_response->data);

                            if (isset($trajectoryInfo['code']) && $trajectoryInfo['code'] == 1) {
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
                        ->label(__('actions.cancel_order'))
                        ->visible(fn (Order $record): bool =>
                        !is_null($record->tracking_number)
                        )
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

                            if (isset($cancelResult['code']) && $cancelResult['code'] == 1) {
                                $record->update([
                                    'status'            => OrderStatus::Cancelled,
                                    'shipping_status'   => 'cancelled',
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
        $previousStatus = $order->status; // No need to use tryFrom()

        // Ensure previous status is valid before performing stock restoration
        if (
            $previousStatus !== null && // Check if previousStatus is valid
            in_array($previousStatus, [OrderStatus::Pending, OrderStatus::Preparing, OrderStatus::Shipping], true) &&
            in_array($status, [OrderStatus::Cancelled, OrderStatus::Refund], true)
        ) {
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('quantity', $item->quantity);
                        $product->inventory()->increment('quantity', $item->quantity);
                    }
                }
            }
        }

        // Update order status
        $order->update(['status' => $status]); // Directly assign the enum value

        // Handle JT Express when the status is set to "Shipping"
        if ($status === OrderStatus::Shipping) {
            $JtExpressOrderData = self::prepareJtExpressOrderData($order);
            $jtExpressResponse = app(JtExpressService::class)->createOrder($JtExpressOrderData);
            self::updateJtExpressOrder($order, 'pending', $JtExpressOrderData, $jtExpressResponse);

            // Send order confirmation email
            $email = $order->user->email ?? $order->contact->email;
            if ($email) {
                Mail::to($email)->queue(new OrderConfirmationMail($order));
            }
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
                Step::make(__('Order Details'))
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
                                // Hide when bundle is selected

                                TextInput::make('subtotal')
                                    ->readOnly()
                                    ->label(__('Subtotal'))
                                    ->numeric()
                                ,
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
