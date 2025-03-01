<?php

namespace App\Filament\Resources;


use Filament\Tables\Table;
use App\Models\LandingPageOrder;
use Filament\Resources\Resource;
use App\Services\JtExpressService;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use App\Filament\Resources\OrderResource\Pages;

class OrderResource extends Resource
{
    protected static ?string $model = LandingPageOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Orders & Contacts';
    protected static ?string $navigationLabel = 'Landing Page Orders';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Order ID')->sortable(),
                TextColumn::make('landingPage.slug')->label('Landing Page')->sortable(),
                TextColumn::make('governorate.name')->label('Governorate')->sortable(),
                TextColumn::make('country.name')->label('Country')->sortable(),
                TextColumn::make('shippingType.name')->label('Shipping Type')->sortable(),
                TextColumn::make('name')->label('Customer Name')->sortable()->searchable(),
                TextColumn::make('phone')->label('Phone Number')->sortable()->searchable(),
                TextColumn::make('another_phone')->label('Alternate Phone')->sortable()->searchable(),
                TextColumn::make('address')->label('Address')->sortable()->limit(50),
                TextColumn::make('quantity')->label('Quantity')->sortable(),
                TextColumn::make('subtotal')->label('Subtotal')->money('USD')->sortable(),
                TextColumn::make('shipping_cost')->label('Shipping Cost')->money('USD')->sortable(),
                TextColumn::make('total')->label('Total Price')->money('USD')->sortable(),
                TextColumn::make('status')->label('Status')->badge()->sortable(),
                TextColumn::make('tracking_number')->label('Tracking Number'),
                TextColumn::make('notes')->label('Notes')->limit(100),
                TextColumn::make('created_at')->label('Order Date')->dateTime('M d, Y H:i A')->sortable(),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('trackOrder')
                        ->label('Track Order')
                        ->icon('heroicon-o-map')
                        ->color('success')
                        ->visible(fn (LandingPageOrder $record): bool =>
                            !is_null($record->tracking_number) &&
                            !is_null($record->shipping_status)
                        )
                        ->action(function (LandingPageOrder $record): void {
                            $trackingInfo = app(JtExpressService::class)->trackLogistics($record->tracking_number);

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
                    Action::make('checkOrder')
                        ->label('Check Order Status')
                        ->icon('heroicon-o-check-circle')
                        ->color('primary')
                        ->visible(fn (LandingPageOrder $record): bool =>
                            !is_null($record->tracking_number)
                        )
                        ->action(function (LandingPageOrder $record): void {
                            $orderInfo = app(JtExpressService::class)->checkingOrder($record->tracking_number);

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
                    Action::make('getOrderStatus')
                        ->label('Get Detailed Status')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->color('info')
                        ->visible(fn (LandingPageOrder $record): bool =>
                            !is_null($record->tracking_number)
                        )
                        ->action(function (LandingPageOrder $record): void {
                            $statusInfo = app(JtExpressService::class)->getOrderStatus($record->tracking_number);

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
                    Action::make('getTrajectory')
                        ->label('View Delivery Trajectory')
                        ->icon('heroicon-o-arrow-path')
                        ->color('gray')
                        ->visible(fn (LandingPageOrder $record): bool =>
                            !is_null($record->tracking_number)
                        )
                        ->action(function (LandingPageOrder $record): void {
                            $trajectoryInfo = app(JtExpressService::class)->getLogisticsTrajectory($record->tracking_number);

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
                    Action::make('cancelOrder')
                        ->label('Cancel Order')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (LandingPageOrder $record): bool =>
                            !is_null($record->tracking_number) &&
                            $record->shipping_status !== 'delivered' &&
                            $record->shipping_status !== 'cancelled'
                        )
                        ->requiresConfirmation()
                        ->action(function (LandingPageOrder $record): void {
                            $cancelResult = app(JtExpressService::class)->cancelOrder($record->tracking_number);

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
                ])
                ->visible(fn (LandingPageOrder $record): bool =>
                    !is_null($record->tracking_number)
                )
                ->label('Shipping Actions')
                ->icon('heroicon-o-truck')
                ->button()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/')
        ];
    }
}
