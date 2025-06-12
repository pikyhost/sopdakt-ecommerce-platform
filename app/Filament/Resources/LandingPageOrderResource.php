<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use Filament\Tables\Table;
use App\Models\LandingPageOrder;
use Filament\Resources\Resource;
use App\Services\JtExpressService;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use App\Filament\Resources\LandingPageOrderResource\Pages\ListLandingPageOrders;
use Filament\Forms\Components\{Grid, Section, Select, TextInput, Textarea};
use Filament\Forms\Form;

class LandingPageOrderResource extends Resource
{
    protected static ?string $model = LandingPageOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function getNavigationGroup(): ?string
    {
        return __('landing_page_order.orders_contacts');
    }

    public static function getNavigationLabel(): string
    {
        return __('Landing Page Orders');
    }

    public static function getModelLabel(): string
    {
        return __('landing_page_order.landing_page_order');
    }

    public static function getPluralLabel(): ?string
    {
        return __('landing_page_order.landing_page_orders');
    }

    public static function getLabel(): ?string
    {
        return __('landing_page_order.landing_page_order');
    }

    public static function getPluralModelLabel(): string
    {
        return __('landing_page_order.landing_page_orders');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('landing_page_order.order_details'))
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('landing_page_id')->relationship('landingPage', 'slug')->required()->label(__('landing_page_order.landing_page')),
                                Select::make('governorate_id')->relationship('governorate', 'name')->required()->label(__('landing_page_order.governorate')),
                                Select::make('country_id')->relationship('country', 'name')->required()->label(__('landing_page_order.country')),
                                Select::make('shipping_type_id')->relationship('shippingType', 'name')->required()->label(__('landing_page_order.shipping_type')),
                                TextInput::make('quantity')->numeric()->minValue(1)->required()->label(__('landing_page_order.quantity')),
                                Select::make('status')->options(collect(OrderStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->getLabel()]))->required()->label(__('landing_page_order.status')),
                            ]),
                    ]),

                Section::make(__('landing_page_order.customer_information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')->required()->label(__('landing_page_order.customer_name')),
                                TextInput::make('phone')->tel()->required()->label(__('landing_page_order.phone_number')),
                                TextInput::make('another_phone')->tel()->label(__('landing_page_order.alternate_phone_number')),
                                Textarea::make('address')->required()->rows(3)->label(__('landing_page_order.address')),
                            ]),
                    ]),

                Section::make(__('landing_page_order.pricing'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('subtotal')->numeric()->prefix('$')->required()->label(__('landing_page_order.subtotal')),
                                TextInput::make('shipping_cost')->numeric()->prefix('$')->required()->label(__('landing_page_order.shipping_cost')),
                                TextInput::make('total')->numeric()->prefix('$')->required()->label(__('landing_page_order.total_price')),
                            ]),
                    ]),

                Section::make(__('landing_page_order.shipping_information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('tracking_number')->label(__('landing_page_order.tracking_number')),
                                TextInput::make('shipping_status')->label(__('landing_page_order.shipping_status')),
                                Textarea::make('notes')->rows(3)->label(__('landing_page_order.notes')),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('landing_page_order.order_id'))->sortable(),
                TextColumn::make('landingPage.slug')->label(__('landing_page_order.landing_page'))->sortable(),
                TextColumn::make('governorate.name')->label(__('landing_page_order.governorate'))->sortable(),
                TextColumn::make('country.name')->label(__('landing_page_order.country'))->sortable(),
                TextColumn::make('shippingType.name')->label(__('landing_page_order.shipping_type'))->sortable(),
                TextColumn::make('name')->label(__('landing_page_order.customer_name'))->sortable()->searchable(),
                TextColumn::make('phone')->label(__('landing_page_order.phone_number'))->sortable()->searchable(),
                TextColumn::make('another_phone')->label(__('landing_page_order.alternate_phone_number'))->sortable()->searchable(),
                TextColumn::make('address')->label(__('landing_page_order.address'))->sortable()->limit(50),
                TextColumn::make('quantity')->label(__('landing_page_order.quantity'))->sortable(),
                TextColumn::make('subtotal')->label(__('landing_page_order.subtotal'))->money('USD')->sortable(),
                TextColumn::make('shipping_cost')->label(__('landing_page_order.shipping_cost'))->money('USD')->sortable(),
                TextColumn::make('total')->label(__('landing_page_order.total_price'))->money('USD')->sortable(),
                TextColumn::make('status')->label(__('landing_page_order.status'))->badge()->sortable(),
                TextColumn::make('tracking_number')->label(__('landing_page_order.tracking_number')),
                TextColumn::make('notes')->label(__('landing_page_order.notes'))->limit(100),
                TextColumn::make('created_at')->label(__('landing_page_order.created_at'))->dateTime('M d, Y H:i A')->sortable(),
            ])
            ->actions([
                EditAction::make()->color('primary'),
                ActionGroup::make([
                    Action::make(OrderStatus::Shipping->getLabel())->label('Change To (Shipping)')->icon('heroicon-m-truck')->color('primary')->action(fn ($record) => self::updateOrderStatus($record, OrderStatus::Shipping)),
                    Action::make('trackOrder')
                        ->label(__('landing_page_order.track_order'))
                        ->icon('heroicon-o-map')
                        ->color('success')
                        ->visible(fn (LandingPageOrder $record): bool =>
                            !is_null($record->tracking_number) &&
                            !is_null($record->shipping_status)
                        )
                        ->action(function (LandingPageOrder $record): void {
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
                    Action::make('checkOrder')
                        ->label(__('landing_page_order.check_order_status'))
                        ->icon('heroicon-o-check-circle')
                        ->color('primary')
                        ->visible(fn (LandingPageOrder $record): bool =>
                            !is_null($record->tracking_number)
                        )
                        ->action(function (LandingPageOrder $record): void {
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
                    Action::make('getOrderStatus')
                        ->label(__('landing_page_order.get_detailed_status'))
                        ->icon('heroicon-o-clipboard-document-list')
                        ->color('info')
                        ->visible(fn (LandingPageOrder $record): bool =>
                            !is_null($record->tracking_number)
                        )
                        ->action(function (LandingPageOrder $record): void {
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
                    Action::make('getTrajectory')
                        ->label(__('landing_page_order.view_delivery_trajectory'))
                        ->icon('heroicon-o-arrow-path')
                        ->color('gray')
                        ->visible(fn (LandingPageOrder $record): bool =>
                            !is_null($record->tracking_number)
                        )
                        ->action(function (LandingPageOrder $record): void {
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
                    Action::make('cancelOrder')
                        ->label(__('landing_page_order.cancel_order'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (LandingPageOrder $record): bool =>
                            !is_null($record->tracking_number) &&
                            $record->shipping_status !== 'delivered' &&
                            $record->shipping_status !== 'cancelled'
                        )
                        ->requiresConfirmation()
                        ->action(function (LandingPageOrder $record): void {
                            $shipping_response = json_decode($record->shipping_response);
                            $cancelResult = app(JtExpressService::class)->cancelOrder($shipping_response->data);

                            if (isset($cancelResult['code']) && $cancelResult['code'] == 1) {
                                $record->update([
                                    'status'            => 'cancelled',
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
                ])
                ->label(__('landing_page_order.shipping_actions'))
                ->icon('heroicon-o-truck')
                ->button()
            ]);
    }

    public static function updateOrderStatus($order, OrderStatus $status)
    {
        $order->update(['status' => $status->value]);

        if ($status === OrderStatus::Shipping) {
            $JtExpressOrderData = self::prepareJtExpressOrderData($order);
            $jtExpressResponse = app(JtExpressService::class)->createOrder($JtExpressOrderData);
            self::updateJtExpressLandingPageOrder($order, 'pending', $JtExpressOrderData, $jtExpressResponse);
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLandingPageOrders::route('/'),
        ];
    }

    private static function prepareJtExpressOrderData($order): array
    {
        $data = [
            'tracking_number'           => 'EGY' . time() . rand(1000, 9999),
            'weight'                    => 1.0,
            'quantity'                  => 1, // $order->quantity,
            'remark'                    => $order->notes ?? '',
            'item_name'                 => $order->landingPage->name ?? 'Product Order',
            'item_quantity'             => $order->quantity,
            'item_value'                => $order->total,
            'item_currency'             => 'EGP',
            'item_description'          => $order->landingPage->description ?? '',
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

    private static function updateJtExpressLandingPageOrder(LandingPageOrder $order, string $shipping_status, $JtExpressOrderData, $jtExpressResponse)
    {
        if (isset($jtExpressResponse['code']) && $jtExpressResponse['code'] == 1) {
            $order->update([
                'tracking_number'   => $JtExpressOrderData['tracking_number'] ?? null,
                'shipping_status'   => $shipping_status,
                'shipping_response' => json_encode($jtExpressResponse)
            ]);
        }
    }
}
