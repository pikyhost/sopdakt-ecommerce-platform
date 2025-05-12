<?php

namespace App\Filament\Resources;

use App\Filament\Exports\OrderExporter;
use App\Models\Contact;
use App\Models\User;
use App\Services\AramexService;
use App\Services\BostaService;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Mail\OrderStatusMail;
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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Parallax\FilamentComments\Tables\Actions\CommentsAction;

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
            ->headerActions([
                ExportAction::make()
                    ->formats([
                        ExportFormat::Xlsx,
                    ])
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->exporter(OrderExporter::class),
                Action::make('back')
                    ->label(__('Back to previous page'))
                    ->icon(app()->getLocale() == 'en' ? 'heroicon-m-arrow-right' : 'heroicon-m-arrow-left')
                    ->iconPosition(IconPosition::After)
                    ->color('gray')
                    ->url(url()->previous())
                    ->hidden(fn() => url()->previous() === url()->current()),
            ])
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

                TextColumn::make('bosta_delivery_id')
                    ->copyable()
                    ->placeholder('-')
                    ->label(__('Bosta Delivery Number'))
                    ->searchable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('user.name')
                    ->formatStateUsing(function ($record) {
                        return $record->user->name . ' (#' . $record->user_id . ')';
                    })
                    ->label(__('User Name'))
                    ->searchable()
                    ->placeholder('-')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contact.name')
                    ->formatStateUsing(function ($record) {
                        return $record->contact->name . ' (#' . $record->contact_id . ')';
                    })
                    ->label(__('Contact Name'))
                    ->searchable()
                    ->placeholder('-')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('user.phone')
                    ->iconColor('primary')
                    ->icon('heroicon-o-phone')
                    ->label(__('User Phone Number'))
                    ->placeholder(__('No phone number saved'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.second_phone')
                    ->label(__('Second Phone Number'))
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('contact.phone')
                    ->iconColor('primary')
                    ->icon('heroicon-o-phone')
                    ->label(__('Contact Phone Number'))
                    ->placeholder(__('No phone number saved'))
                    ->searchable(),

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
                            ->mapWithKeys(fn($status) => [$status->value => $status->getLabel()])
                            ->toArray()
                    ),

                Filter::make('location')
                    ->form([
                        Select::make('country_ids')
                            ->label(__('Countries'))
                            ->multiple()
                            ->live()
                            ->options(fn() => Country::pluck('name', 'id')),

                        Select::make('governorate_ids')
                            ->label(__('Governorates'))
                            ->multiple()
                            ->live()
                            ->searchable()
                            ->options(function (callable $get) {
                                $countryIds = $get('country_ids');

                                if (!is_array($countryIds) || empty($countryIds)) {
                                    return [];
                                }

                                return Governorate::whereIn('country_id', $countryIds)
                                    ->pluck('name', 'id');
                            }),

                        Select::make('city_ids')
                            ->label(__('Cities'))
                            ->multiple()
                            ->searchable()
                            ->options(function (callable $get) {
                                $governorateIds = $get('governorate_ids');

                                if (!is_array($governorateIds) || empty($governorateIds)) {
                                    return [];
                                }

                                return City::whereIn('governorate_id', $governorateIds)
                                    ->pluck('name', 'id');
                            }),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(filled($data['country_ids'] ?? null), function (Builder $q) use ($data) {
                                $q->whereIn('country_id', $data['country_ids']);
                            })
                            ->when(filled($data['governorate_ids'] ?? null), function (Builder $q) use ($data) {
                                $q->whereIn('governorate_id', $data['governorate_ids']);
                            })
                            ->when(filled($data['city_ids'] ?? null), function (Builder $q) use ($data) {
                                $q->whereIn('city_id', $data['city_ids']);
                            });
                    })
                    ->indicateUsing(function (array $data) {
                        $indicators = [];

                        if (!empty($data['country_ids'])) {
                            $countries = Country::whereIn('id', $data['country_ids'])->pluck('name')->implode(', ');
                            $indicators[] = Indicator::make("Countries: $countries")->removeField('country_ids');
                        }

                        if (!empty($data['governorate_ids'])) {
                            $governorates = Governorate::whereIn('id', $data['governorate_ids'])->pluck('name')->implode(', ');
                            $indicators[] = Indicator::make("Governorates: $governorates")->removeField('governorate_ids');
                        }

                        if (!empty($data['city_ids'])) {
                            $cities = City::whereIn('id', $data['city_ids'])->pluck('name')->implode(', ');
                            $indicators[] = Indicator::make("Cities: $cities")->removeField('city_ids');
                        }

                        return $indicators;
                    }),

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
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
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
                Tables\Actions\Action::make('trackOrder')
                ->label(__('actions.track_order'))
                ->icon('heroicon-o-map')
                ->color('success')
                ->visible(fn(Order $record): bool => Setting::first()?->enable_jnt && !is_null($record->tracking_number))
                ->action(function (Order $record): void {
                    try {
                        $shipping_response = json_decode($record->shipping_response, true);
                        if (json_last_error() !== JSON_ERROR_NONE || !is_array($shipping_response)) {
                            Log::error('Invalid shipping_response', ['order_id' => $record->id, 'shipping_response' => $record->shipping_response]);
                            Notification::make()->title('Tracking Error')->body('Invalid shipping response data')->danger()->send();
                            return;
                        }

                        $response_data = isset($shipping_response['data']) ? $shipping_response['data'] : $shipping_response;
                        $txlogisticId = $response_data['txlogisticId'] ?? preg_replace('/^#5\s*/', '', $record->tracking_number);
                        $billCode = $response_data['billCode'] ?? preg_replace('/^#5\s*/', '', $record->tracking_number);

                        if (!$txlogisticId || !$billCode) {
                            Log::error('Missing tracking data', ['order_id' => $record->id, 'txlogisticId' => $txlogisticId, 'billCode' => $billCode, 'response_data' => $response_data]);
                            Notification::make()->title('Tracking Error')->body('Missing required tracking data (txlogisticId or billCode)')->danger()->send();
                            return;
                        }

                        $tracking_data = (object) [
                            'txlogisticId' => $txlogisticId,
                            'billCode' => $billCode,
                            'sortingCode' => $response_data['sortingCode'] ?? '',
                            'createOrderTime' => $response_data['createOrderTime'] ?? now()->toDateTimeString(),
                            'lastCenterName' => $response_data['lastCenterName'] ?? '',
                        ];

                        $trackingInfo = app(JtExpressService::class)->trackLogistics($tracking_data);

                        if (($trackingInfo['code'] ?? 0) === 1) {
                            Notification::make()->title('Tracking Information')->body('Tracking details retrieved successfully: ' . ($trackingInfo['data']['billCode'] ?? $record->tracking_number))->success()->send();
                        } else {
                            Log::error('Track Order Failed', ['order_id' => $record->id, 'response' => $trackingInfo]);
                            Notification::make()->title('Tracking Error')->body($trackingInfo['msg'] ?? 'Unable to retrieve tracking information')->danger()->send();
                        }
                    } catch (\Exception $e) {
                        Log::error('Track Order Error', ['order_id' => $record->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                        Notification::make()->title('Tracking Error')->body('An error occurred: ' . $e->getMessage())->danger()->send();
                    }
                }),

                Tables\Actions\Action::make('checkOrder')
                    ->label(__('actions.check_order'))
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->visible(fn(Order $record): bool => Setting::first()?->enable_jnt && !is_null($record->tracking_number))
                    ->action(function (Order $record): void {
                        try {
                            $shipping_response = json_decode($record->shipping_response, true);
                            if (json_last_error() !== JSON_ERROR_NONE || !is_array($shipping_response)) {
                                Log::error('Invalid shipping_response', ['order_id' => $record->id, 'shipping_response' => $record->shipping_response]);
                                Notification::make()->title('Check Error')->body('Invalid shipping response data')->danger()->send();
                                return;
                            }

                            $response_data = isset($shipping_response['data']) ? $shipping_response['data'] : $shipping_response;
                            $txlogisticId = $response_data['txlogisticId'] ?? preg_replace('/^#5\s*/', '', $record->tracking_number);
                            $billCode = $response_data['billCode'] ?? preg_replace('/^#5\s*/', '', $record->tracking_number);

                            if (!$txlogisticId || !$billCode) {
                                Log::error('Missing check order data', ['order_id' => $record->id, 'txlogisticId' => $txlogisticId, 'billCode' => $billCode, 'response_data' => $response_data]);
                                Notification::make()->title('Check Error')->body('Missing required order data (txlogisticId or billCode)')->danger()->send();
                                return;
                            }

                            $check_data = (object) [
                                'txlogisticId' => $txlogisticId,
                                'billCode' => $billCode,
                                'sortingCode' => $response_data['sortingCode'] ?? '',
                                'createOrderTime' => $response_data['createOrderTime'] ?? now()->toDateTimeString(),
                                'lastCenterName' => $response_data['lastCenterName'] ?? '',
                            ];

                            $orderInfo = app(JtExpressService::class)->checkingOrder($check_data);

                            if (($orderInfo['code'] ?? 0) === 1) {
                                Notification::make()->title('Order Status')->body('Order exists: ' . ($orderInfo['data']['isExist'] ?? 'Unknown'))->success()->send();
                            } else {
                                Log::error('Check Order Failed', ['order_id' => $record->id, 'response' => $orderInfo]);
                                Notification::make()->title('Check Error')->body($orderInfo['msg'] ?? 'Unable to check order status')->danger()->send();
                            }
                        } catch (\Exception $e) {
                            Log::error('Check Order Error', ['order_id' => $record->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                            Notification::make()->title('Check Error')->body('An error occurred: ' . $e->getMessage())->danger()->send();
                        }
                    }),

                Tables\Actions\Action::make('getOrderStatus')
                    ->label(__('actions.get_order_status'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('info')
                    ->visible(fn(Order $record): bool => Setting::first()?->enable_jnt && !is_null($record->tracking_number))
                    ->action(function (Order $record): void {
                        try {
                            $shipping_response = json_decode($record->shipping_response, true);
                            if (json_last_error() !== JSON_ERROR_NONE || !is_array($shipping_response)) {
                                Log::error('Invalid shipping_response', ['order_id' => $record->id, 'shipping_response' => $record->shipping_response]);
                                Notification::make()->title('Status Error')->body('Invalid shipping response data')->danger()->send();
                                return;
                            }

                            $response_data = isset($shipping_response['data']) ? $shipping_response['data'] : $shipping_response;
                            $txlogisticId = $response_data['txlogisticId'] ?? preg_replace('/^#5\s*/', '', $record->tracking_number);
                            $billCode = $response_data['billCode'] ?? preg_replace('/^#5\s*/', '', $record->tracking_number);

                            if (!$txlogisticId || !$billCode) {
                                Log::error('Missing status data', ['order_id' => $record->id, 'txlogisticId' => $txlogisticId, 'billCode' => $billCode, 'response_data' => $response_data]);
                                Notification::make()->title('Status Error')->body('Missing required status data (txlogisticId or billCode)')->danger()->send();
                                return;
                            }

                            $status_data = (object) [
                                'txlogisticId' => $txlogisticId,
                                'billCode' => $billCode,
                                'sortingCode' => $response_data['sortingCode'] ?? '',
                                'createOrderTime' => $response_data['createOrderTime'] ?? now()->toDateTimeString(),
                                'lastCenterName' => $response_data['lastCenterName'] ?? '',
                            ];

                            $statusInfo = app(JtExpressService::class)->getOrderStatus($status_data);

                            if (($statusInfo['code'] ?? 0) === 1) {
                                $status = $statusInfo['data']['deliveryStatus'] ?? 'Unknown';
                                $record->update([
                                    'shipping_status' => $status,
                                    'shipping_response' => json_encode($statusInfo)
                                ]);
                                Notification::make()->title('Order Status Updated')->body('Current status: ' . $status)->success()->send();
                            } else {
                                Log::error('Get Status Failed', ['order_id' => $record->id, 'response' => $statusInfo]);
                                Notification::make()->title('Status Error')->body($statusInfo['msg'] ?? 'Unable to get order status')->danger()->send();
                            }
                        } catch (\Exception $e) {
                            Log::error('Get Status Error', ['order_id' => $record->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                            Notification::make()->title('Status Error')->body('An error occurred: ' . $e->getMessage())->danger()->send();
                        }
                    }),

                Tables\Actions\Action::make('getTrajectory')
                    ->label(__('actions.get_trajectory'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->visible(fn(Order $record): bool => Setting::first()?->enable_jnt && !is_null($record->tracking_number))
                    ->action(function (Order $record): void {
                        try {
                            $shipping_response = json_decode($record->shipping_response, true);
                            if (json_last_error() !== JSON_ERROR_NONE || !is_array($shipping_response)) {
                                Log::error('Invalid shipping_response', ['order_id' => $record->id, 'shipping_response' => $record->shipping_response]);
                                Notification::make()->title('Trajectory Error')->body('Invalid shipping response data')->danger()->send();
                                return;
                            }

                            $response_data = isset($shipping_response['data']) ? $shipping_response['data'] : $shipping_response;
                            $txlogisticId = $response_data['txlogisticId'] ?? preg_replace('/^#5\s*/', '', $record->tracking_number);
                            $billCode = $response_data['billCode'] ?? preg_replace('/^#5\s*/', '', $record->tracking_number);

                            if (!$txlogisticId || !$billCode) {
                                Log::error('Missing trajectory data', ['order_id' => $record->id, 'txlogisticId' => $txlogisticId, 'billCode' => $billCode, 'response_data' => $response_data]);
                                Notification::make()->title('Trajectory Error')->body('Missing required trajectory data (txlogisticId or billCode)')->danger()->send();
                                return;
                            }

                            $trajectory_data = (object) [
                                'txlogisticId' => $txlogisticId,
                                'billCode' => $billCode,
                                'sortingCode' => $response_data['sortingCode'] ?? '',
                                'createOrderTime' => $response_data['createOrderTime'] ?? now()->toDateTimeString(),
                                'lastCenterName' => $response_data['lastCenterName'] ?? '',
                            ];

                            $trajectoryInfo = app(JtExpressService::class)->getLogisticsTrajectory($trajectory_data);

                            if (($trajectoryInfo['code'] ?? 0) === 1) {
                                $steps = count($trajectoryInfo['data']['details'] ?? []);
                                Notification::make()->title('Delivery Trajectory')->body("Retrieved {$steps} tracking events for this shipment.")->success()->send();
                            } else {
                                Log::error('Get Trajectory Failed', ['order_id' => $record->id, 'response' => $trajectoryInfo]);
                                Notification::make()->title('Trajectory Error')->body($trajectoryInfo['msg'] ?? 'Unable to get trajectory information')->danger()->send();
                            }
                        } catch (\Exception $e) {
                            Log::error('Get Trajectory Error', ['order_id' => $record->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                            Notification::make()->title('Trajectory Error')->body('An error occurred: ' . $e->getMessage())->danger()->send();
                        }
                    }),

                Tables\Actions\Action::make('cancelOrder')
                    ->label(__('actions.cancel_order'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(Order $record): bool => Setting::first()?->enable_jnt && !is_null($record->tracking_number) && $record->shipping_status !== 'delivered' && $record->shipping_status !== 'cancelled')
                    ->action(function (Order $record): void {
                        try {
                            $shipping_response = json_decode($record->shipping_response, true);
                            if (json_last_error() !== JSON_ERROR_NONE || !is_array($shipping_response)) {
                                Log::error('Invalid shipping_response', ['order_id' => $record->id, 'shipping_response' => $record->shipping_response]);
                                Notification::make()->title('Cancellation Error')->body('Invalid shipping response data')->danger()->send();
                                return;
                            }

                            $response_data = isset($shipping_response['data']) ? $shipping_response['data'] : $shipping_response;
                            $txlogisticId = $response_data['txlogisticId'] ?? preg_replace('/^#5\s*/', '', $record->tracking_number);
                            $billCode = $response_data['billCode'] ?? preg_replace('/^#5\s*/', '', $record->tracking_number);

                            if (!$txlogisticId || !$billCode) {
                                Log::error('Missing cancel order data', ['order_id' => $record->id, 'txlogisticId' => $txlogisticId, 'billCode' => $billCode, 'response_data' => $response_data]);
                                Notification::make()->title('Cancellation Error')->body('Missing required cancel data (txlogisticId or billCode)')->danger()->send();
                                return;
                            }

                            $cancel_data = (object) [
                                'txlogisticId' => $txlogisticId,
                                'billCode' => $billCode,
                                'sortingCode' => $response_data['sortingCode'] ?? '',
                                'createOrderTime' => $response_data['createOrderTime'] ?? now()->toDateTimeString(),
                                'lastCenterName' => $response_data['lastCenterName'] ?? '',
                            ];

                            $cancelResult = app(JtExpressService::class)->cancelOrder($cancel_data);

                            if (($cancelResult['code'] ?? 0) === 1) {
                                $record->update([
                                    'status' => OrderStatus::Cancelled,
                                    'shipping_status' => 'cancelled',
                                    'shipping_response' => json_encode($cancelResult)
                                ]);
                                Notification::make()->title('Order Cancelled')->body('The order has been successfully cancelled.')->success()->send();
                            } else {
                                Log::error('Cancel Order Failed', ['order_id' => $record->id, 'response' => $cancelResult]);
                                Notification::make()->title('Cancellation Error')->body($cancelResult['msg'] ?? 'Unable to cancel the order')->danger()->send();
                            }
                        } catch (\Exception $e) {
                            Log::error('Cancel Order Error', ['order_id' => $record->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                            Notification::make()->title('Cancellation Error')->body('An error occurred: ' . $e->getMessage())->danger()->send();
                        }
                    }),

                Action::make('send_to_bosta')
                    ->label('Send to Bosta')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->visible(fn(Order $record): bool => Setting::first()?->enable_bosta &&
                        $record->status === OrderStatus::Preparing &&
                        !$record->bosta_delivery_id &&
                        ($record->user || $record->contact) &&
                        ($record->user ? $record->user->addresses()->where('is_primary', true)->exists() : $record->contact->address) &&
                        $record->city?->bosta_code &&
                        ($record->user?->phone ?? $record->contact?->phone)
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Send Order to Bosta')
                    ->modalDescription('This will create a delivery in Bosta and set the order status to Shipping.')
                    ->modalSubmitActionLabel('Confirm')
                    ->action(function (Order $record) {
                        $bostaService = new BostaService();
                        $response = $bostaService->createDelivery($record);

                        if ($response && isset($response['data']['_id'])) {
                            $record->update([
                                'status' => OrderStatus::Shipping,
                                'bosta_delivery_id' => $response['data']['trackingNumber'], //  Use tracking number here
                            ]);
                            Notification::make()->title('Order sent to Bosta')->success()->send();
                        } else {
                            Notification::make()
                                ->title('Failed to send order to Bosta')
                                ->body('Check logs for API error details')
                                ->danger()
                                ->send();
                            Log::error('Failed to create Bosta delivery for order.', ['order_id' => $record->id, 'response' => $response]);
                        }
                    }),


                Action::make('createAramexShipment')
                    ->label('Create ARAMEX Shipment')
                    ->icon('heroicon-o-truck')
                    ->action(function (Order $record, AramexService $aramexService) {
                        try {
                            // Get contact information safely
                            $contact = $record->user ?? $record->contact;
                            $city = $record->city;
                            $country = $record->country;
                            $governorate = $record->governorate;

                            // Validate required information
                            if (!$contact || !$city || !$country) {
                                Notification::make()
                                    ->title('Cannot create ARAMEX shipment')
                                    ->body('Missing contact, city, or country information')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Get address line
                            $addressLine1 = null;
                            if ($contact instanceof \App\Models\User) {
                                $primaryAddress = $contact->addresses()->where('is_primary', true)->first();
                                $addressLine1 = $primaryAddress?->address;
                            } else {
                                $addressLine1 = $contact->address ?? null;
                            }

                            if (!$addressLine1) {
                                Notification::make()
                                    ->title('Cannot create ARAMEX shipment')
                                    ->body('Missing contact address information')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Prepare name
                            $fullName = $contact->name ?? $contact->full_name ?? 'Unknown Name';
                            $nameParts = explode(' ', $fullName);
                            $firstName = $nameParts[0] ?? 'First';
                            $lastName = $nameParts[1] ?? 'Last';

                            // Prepare shipment data
                            $shipmentData = [
                                'reference' => 'ORDER-' . $record->id,
                                'weight' => $record->items->sum('weight') ?: 1,
                                'description' => 'Order #' . $record->id,
                                'product_group' => 'DOM',
                                'product_type' => 'OND',
                                'payment_type' => 'P',
                            ];

                            // Shipper data (your store info)
                            $shipperData = [
                                'Reference1' => 'STORE-' . $record->id,
                                'Reference2' => '',
                                'AccountNumber' => config('services.aramex.account_number'),
                                'PartyAddress' => [
                                    'Line1' => '123 Main St',
                                    'Line2' => '',
                                    'Line3' => '',
                                    'City' => 'Cairo',
                                    'StateOrProvinceCode' => 'C',
                                    'PostCode' => '11511',
                                    'CountryCode' => 'EG',
                                ],
                                'Contact' => [
                                    'Department' => '',
                                    'PersonName' => 'Store Manager',
                                    'Title' => '',
                                    'CompanyName' => 'Your Store Name',
                                    'PhoneNumber1' => '+201000000000',
                                    'PhoneNumber1Ext' => '',
                                    'PhoneNumber2' => '',
                                    'PhoneNumber2Ext' => '',
                                    'FaxNumber' => '',
                                    'CellPhone' => '+201000000000',
                                    'EmailAddress' => 'store@example.com',
                                    'Type' => ''
                                ],
                            ];

                            // Consignee data (customer info)
                            $consigneeData = [
                                'Reference1' => 'CUSTOMER-' . $record->user_id,
                                'Reference2' => '',
                                'AccountNumber' => '',
                                'PartyAddress' => [
                                    'Line1' => $addressLine1,
                                    'Line2' => $addressLine1 ?? '',
                                    'Line3' => '',
                                    'City' => $city->name,
                                    'StateOrProvinceCode' => $governorate->code ?? 'C',
                                    'PostCode' => $contact->postal_code ?? '00000',
                                    'CountryCode' => $country->code,
                                ],
                                'Contact' => [
                                    'Department' => '',
                                    'PersonName' => $fullName,
                                    'Title' => '',
                                    'CompanyName' => '',
                                    'PhoneNumber1' => $contact->phone ?? $contact->phone ?? '+201234567890',
                                    'PhoneNumber1Ext' => '',
                                    'PhoneNumber2' => '',
                                    'PhoneNumber2Ext' => '',
                                    'FaxNumber' => '',
                                    'CellPhone' => $contact->phone ?? $contact->phone ?? '+201234567890',
                                    'EmailAddress' => $contact->email ?? 'customer@example.com',
                                    'Type' => ''
                                ],
                            ];

                            // Prepare items
                            $items = [];
                            foreach ($record->items as $item) {
                                $items[] = [
                                    'PackageType' => 'Box',
                                    'Quantity' => $item->quantity,
                                    'Weight' => [
                                        'Value' => $item->weight ?: 0.5,
                                        'Unit' => 'kg'
                                    ],
                                    'Comments' => $item->product->name,
                                    'Reference' => 'ITEM-' . $item->id,
                                ];
                            }

                            // Create shipment directly
                            $response = $aramexService->createShipment($shipmentData, $shipperData, $consigneeData, $items);

                            // Update order
                            $record->update([
                                'aramex_shipment_id' => $response->Shipments->ProcessedShipment->ID,
                                'aramex_tracking_number' => $response->Shipments->ProcessedShipment->ShipmentNumber,
                                'aramex_tracking_url' => 'https://www.aramex.com/track/results?ShipmentNumber=' . $response->Shipments->ProcessedShipment->ShipmentNumber,
                                'aramex_response' => json_encode($response),
                                'status' => 'shipping',
                            ]);

                            Notification::make()
                                ->title('Shipment Created Successfully')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to create shipment')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Create ARAMEX Shipment')
                    ->modalSubheading('Are you sure you want to create an ARAMEX shipment for this order?')
                    ->modalButton('Create Shipment'),


                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    CommentsAction::make()->color('primary'),
                    Tables\Actions\EditAction::make(),
                    ...collect(OrderStatus::cases())->map(fn($status) => Tables\Actions\Action::make($status->value)
                        ->label(__($status->getLabel()))
                        ->icon($status->getIcon())
                        ->color($status->getColor())
                        ->action(fn($record) => self::updateOrderStatus($record, $status))
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
                    ...collect(OrderStatus::cases())->map(fn($status) => Tables\Actions\BulkAction::make($status->value)
                        ->label(__($status->getLabel()))
                        ->icon($status->getIcon())
                        ->color($status->getColor())
                        ->action(fn($records) => $records->each(fn($record) => $record->update(['status' => $status->value])))
                    )->toArray(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function updateOrderStatus($order, OrderStatus $status)
    {
        $previousStatus = $order->status;
        $newStatus = $status->value; // Convert Enum to string
        $order->refresh(); // Ensure latest data before updating

        // Restore inventory when canceling or refunding an order
        if (
            $previousStatus !== null &&
            in_array($previousStatus, [OrderStatus::Pending->value, OrderStatus::Preparing->value, OrderStatus::Shipping->value], true) &&
            in_array($newStatus, [OrderStatus::Cancelled->value, OrderStatus::Refund->value], true)
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
        $order->status = $newStatus;

        // ✅ If Shipping, first get the tracking number from J&T Express
        if ($newStatus === OrderStatus::Shipping->value) {
            $JtExpressOrderData = self::prepareJtExpressOrderData($order);
            $jtExpressResponse = app(JtExpressService::class)->createOrder($JtExpressOrderData);

            // Extract tracking number
            $trackingNumber = $jtExpressResponse['data']['txlogisticId'] ?? null;
            if ($trackingNumber) {
                $order->tracking_number = $trackingNumber;
            }

            // Save order with updated tracking number
            $order->save();

            // Update tracking details in J&T system
            self::updateJtExpressOrder($order, 'pending', $JtExpressOrderData, $jtExpressResponse);
        } else {
            // Save order if it's not Shipping
            $order->save();
        }

        // ✅ Refresh the order to make sure tracking number is loaded
        $order->refresh();

        // ✅ Send email AFTER tracking number is updated
        $email = $order->user->email ?? $order->contact->email;
        if ($email) {
            Mail::to($email)->send(new OrderStatusMail($order, $status));
        }
    }

    private static function prepareJtExpressOrderData($order): array
    {
        $data = [
            'tracking_number' => time() . rand(1000, 9999),
            'weight' => 1.0, // You might want to calculate the total weight dynamically
            'quantity' => 1,  // $order->items->sum('quantity') Sum of all item quantities in the order

            'remark' => implode(' , ', array_filter([
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

            'item_name' => $order->items->pluck('product.name')->implode(', '), // Concatenated product names
            'item_quantity' => $order->items->count(), // Total distinct items in the order
            'item_value' => $order->total, // Order total amount
            'item_currency' => 'EGP',
            'item_description' => $order->notes ?? 'No description provided',
        ];

        $data['sender'] = [
            'name' => 'Your Company Name',
            'company' => 'Your Company',
            'city' => 'Your City',
            'address' => 'Your Full Address',
            'mobile' => 'Your Contact Number',
            'countryCode' => 'Your Country Code',
            'prov' => 'Your Prov',
            'area' => 'Your Area',
            'town' => 'Your Town',
            'street' => 'Your Street',
            'addressBak' => 'Your Address Bak',
            'postCode' => 'Your Post Code',
            'phone' => 'Your Phone',
            'mailBox' => 'Your Mail Box',
            'areaCode' => 'Your Area Code',
            'building' => 'Your Building',
            'floor' => 'Your Floor',
            'flats' => 'Your Flats',
            'alternateSenderPhoneNo' => 'Your Alternate Sender Phone No',
        ];

        $data['receiver'] = [
            'name' => 'test', // $order->name,
            'prov' => 'أسيوط', // $order->region->governorate->name,
            'city' => 'القوصية', // $order->region->name,
            'address' => 'sdfsacdscdscdsa', // $order->address,
            'mobile' => '1441234567', // $order->phone,
            'company' => 'guangdongshengshenzhe',
            'countryCode' => 'EGY',
            'area' => 'الصبحه',
            'town' => 'town',
            'addressBak' => 'receivercdsfsafdsaf lkhdlksjlkfjkndskjfnhskjlkafdslkjdshflksjal',
            'street' => 'street',
            'postCode' => '54830',
            'phone' => '23423423423',
            'mailBox' => 'ant_li123@qq.com',
            'areaCode' => '2342343',
            'building' => '13',
            'floor' => '25',
            'flats' => '47',
            'alternateReceiverPhoneNo' => $order->another_phone ?? '1231321322',
        ];

        return $data;
    }

    private static function updateJtExpressOrder(Order $order, string $shipping_status, $JtExpressOrderData, $jtExpressResponse)
    {
        if (isset($jtExpressResponse['code']) && $jtExpressResponse['code'] == 1) {
            $order->update([
                'tracking_number' => $JtExpressOrderData['tracking_number'] ?? null,
                'shipping_status' => $shipping_status,
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
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('checkout_token')
                    ->default(fn() => (string)Str::uuid())
                    ->dehydrated(),
                Wizard::make([
                    Step::make(__('Order Details'))
                        ->schema([
                            Select::make('user_id')
                                ->live()
                                ->hidden(fn(Get $get) => $get('contact_id'))
                                ->relationship('user', 'name')
                                ->label(__('Customer')),

                            Select::make('contact_id')
                                ->label(__('Contact'))
                                ->relationship(
                                    name: 'contact',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query) =>
                                    $query->whereNotNull('name')
                                )
                                ->visible(fn(Get $get) => !$get('user_id'))
                                ->live(),


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
                                        ->afterStateUpdated(fn($state, Forms\Set $set) => $set('price_per_unit', (float)Product::find($state)?->discount_price_for_current_country ?? 0)
                                        )->disabled(fn($record, $operation) => $operation === 'edit'
                                            && $record?->bundle_id !== null)
                                    ,

                                    Select::make('color_id')
                                        ->label(__('Color'))
                                        ->options(fn(Get $get) => ProductColor::where('product_id', $get('product_id'))
                                            ->with('color')
                                            ->get()
                                            ->pluck('color.name', 'color.id'))
                                        ->reactive()
                                        ->afterStateUpdated(fn($state, Forms\Set $set) => $set('size_id', null))
                                        ->visible(fn(Get $get) => Product::find($get('product_id'))?->productColors()->exists()),

                                    Select::make('size_id')
                                        ->label(__('Size'))
                                        ->options(fn(Get $get) => ProductColor::where([
                                            ['product_id', $get('product_id')],
                                            ['color_id', $get('color_id')],
                                        ])
                                            ->with('sizes')
                                            ->first()?->sizes
                                            ->pluck('name', 'id') ?? [])
                                        ->reactive()
                                        ->visible(fn(Get $get) => Product::find($get('product_id'))?->productColors()->exists()),

                                    TextInput::make('quantity')
                                        ->minValue(1)
                                        ->required()
                                        ->label(__('Quantity'))
                                        ->numeric()
                                        ->minValue(1)
                                        ->live()
                                        ->disabled(fn($record, $operation) => $operation === 'edit'
                                            && $record?->bundle_id !== null)
                                        ->afterStateUpdated(fn($state, callable $set, Get $get) => $set('subtotal', ($get('price_per_unit') ?? 0) * ($state ?? 1))
                                        ),

                                    TextInput::make('price_per_unit')
                                        ->default(fn(Get $get) => (float)Product::find($get('product_id'))?->discount_price_for_current_country ?? 0)
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
                                        ->filter(fn($item) => !isset($item['bundle_id']) || empty($item['bundle_id']))
                                        ->sum(fn($item) => ($item['subtotal'] ?? 0));

                                    // Check free shipping threshold - NEW CODE
                                    $freeShippingThreshold = Setting::getFreeShippingThreshold();
                                    if ($freeShippingThreshold > 0 && $subtotal >= $freeShippingThreshold) {
                                        $shippingCost = 0.0;
                                        $set('shipping_cost', 0.0);
                                    }

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
                                ->afterStateUpdated(fn($state, callable $set, Get $get) => self::updateShippingCost($set, $state, $get('items'), $get('city_id'), $get('governorate_id'), $get('country_id'))
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
        // Calculate subtotal first
        $subtotal = collect($items)->sum(fn($item) => $item['subtotal'] ?? 0);
        $freeShippingThreshold = Setting::getFreeShippingThreshold();

        // Check free shipping threshold
        if ($freeShippingThreshold > 0 && $subtotal >= $freeShippingThreshold) {
            $set('shipping_cost', 0.0);
            return;
        }

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
        $subtotal = $items->sum(fn($item) => $item['subtotal'] ?? 0);

        if ($bundleId = $get('bundle_id')) {
            $subtotal += Bundle::find($bundleId)?->discount_price ?? 0;
        }

        $taxPercentage = self::getTaxPercentage();
        $taxAmount = ($subtotal * $taxPercentage) / 100;

        // Check free shipping threshold
        $freeShippingThreshold = Setting::getFreeShippingThreshold();
        $shippingCost = ($freeShippingThreshold > 0 && $subtotal >= $freeShippingThreshold)
            ? 0.0
            : ($get('shipping_cost') ?? 0);

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
            if ($city?->cost > 0) return (float)$city->cost;
        }

        if ($governorateId) {
            $governorate = Governorate::find($governorateId);
            if ($governorate?->cost > 0) return (float)$governorate->cost;

            $zone = $governorate->shippingZones()->first();
            if ($zone?->cost > 0) return (float)$zone->cost;
        }

        if ($countryId) {
            $country = Country::find($countryId);
            if ($country?->cost > 0) return (float)$country->cost;
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
