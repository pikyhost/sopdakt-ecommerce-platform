<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label(__('ID')),
            ExportColumn::make('name')
                ->label(__('Name')),
            ExportColumn::make('orders_count')
                ->counts('orders')
                ->label(__('Orders Count')), // New column added
            ExportColumn::make('email')
                ->label(__('Email')),
            ExportColumn::make('preferred_language')
                ->label(__('Preferred Language')),
            ExportColumn::make('email_verified_at')
                ->label(__('Email Verified At')),
            ExportColumn::make('phone')
                ->label(__('Phone')),
            ExportColumn::make('second_phone')
                ->label(__('Second Phone')),
            ExportColumn::make('created_at')
                ->label(__('Created At')),
            ExportColumn::make('updated_at')
                ->label(__('Updated At')),
            ExportColumn::make('is_active')
                ->label(__('Is Active')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your user export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
