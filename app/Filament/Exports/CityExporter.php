<?php

namespace App\Filament\Exports;

use App\Models\City;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CityExporter extends Exporter
{
    protected static ?string $model = City::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')   ->label(__('id')),
            ExportColumn::make('name') ->label(__('name')),
            ExportColumn::make('governorat.name')->label(__('governorate_name')),
            ExportColumn::make('cost')    ->label(__('Shipping Cost')),
            ExportColumn::make('shipping_estimate_time')  ->label(__('shipping_cost.shipping_estimate_time')),
            ExportColumn::make('created_at')   ->label(__('Created At')),
            ExportColumn::make('updated_at')->label(__('Updated At')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your city export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
