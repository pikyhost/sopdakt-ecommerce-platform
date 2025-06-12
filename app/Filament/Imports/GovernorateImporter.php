<?php

namespace App\Filament\Imports;

use App\Models\Governorate;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class GovernorateImporter extends Importer
{
    protected static ?string $model = Governorate::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label(__('name'))
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('country_id')
                ->label(__('Country'))
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('cost')
                ->label(__('Shipping Cost'))
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('shipping_estimate_time')
                ->label(__('shipping_cost.shipping_estimate_time'))
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Governorate
    {
        // return Governorate::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Governorate();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your governorate import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
