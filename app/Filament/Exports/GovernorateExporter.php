<?php

namespace App\Filament\Exports;

use App\Models\Governorate;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class GovernorateExporter extends Exporter
{
    protected static ?string $model = Governorate::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')   ->label(__('id')),
            ExportColumn::make('name') ->label(__('name')),
            ExportColumn::make('country_id')   ->label(__('Country')),
            ExportColumn::make('cost')  ->label(__('Shipping Cost')),
            ExportColumn::make('shipping_estimate_time')->label(__('shipping_cost.shipping_estimate_time')),
            ExportColumn::make('created_at')   ->label(__('Created At')),
            ExportColumn::make('updated_at')->label(__('Updated At')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your governorate export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
