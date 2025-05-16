<?php

namespace App\Filament\Exports;

use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class OrderExporter extends Exporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label(__('ID')),
            ExportColumn::make('user.name')
                ->label(__('User Name')),
            ExportColumn::make('contact.name')
                ->label(__('Contact Name')),
            ExportColumn::make('tracking_number')
                ->label(__('J&T Express Tracking Number')),
            ExportColumn::make('bosta_delivery_id')
                ->label(__('Bosta Tracking Number')),
            ExportColumn::make('aramex_tracking_number')
                ->label(__('Aramex Tracking Number')),
            ExportColumn::make('paymentMethod.name')
                ->label(__('Payment Method')),
            ExportColumn::make('coupon.id')
                ->label(__('Coupon ID')),
            ExportColumn::make('shipping_cost')
                ->label(__('Shipping Cost')),
            ExportColumn::make('tax_percentage')
                ->label(__('Tax Percentage')),
            ExportColumn::make('tax_amount')
                ->label(__('Tax Amount')),
            ExportColumn::make('subtotal')
                ->label(__('Subtotal')),
            ExportColumn::make('total')
                ->label(__('Total')),
            ExportColumn::make('shippingType.name')
                ->label(__('Shipping Type')),
            ExportColumn::make('country.name')
                ->label(__('Country')),
            ExportColumn::make('governorate.name')
                ->label(__('Governorate')),
            ExportColumn::make('city.name')
                ->label(__('City')),
            ExportColumn::make('status')
                ->label(__('Status'))
                ->formatStateUsing(fn ($state) => $state?->value),
            ExportColumn::make('notes')
                ->label(__('Notes')),
            ExportColumn::make('created_at')
                ->label(__('Created At')),
            ExportColumn::make('updated_at')
                ->label(__('Updated At')),
        ];
    }
    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your order export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
