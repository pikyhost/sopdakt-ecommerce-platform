<?php

namespace App\Filament\Imports;

use App\Models\City;
use App\Models\Governorate;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class CityImporter extends Importer
{
    protected static ?string $model = City::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('governorate')
                ->label('Governorate Name')
                ->relationship(
                    resolveUsing: function ($state) {
                        if (!is_string($state) || empty(trim($state))) {
                            Log::error('Invalid governorate name value: ' . json_encode($state));
                            return null;
                        }

                        $governorateName = trim($state);

                        // Try to match in English or Arabic fields (case-insensitive)
                        $governorate = Governorate::whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.en"))) = ?', [strtolower($governorateName)])
                            ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.ar"))) = ?', [strtolower($governorateName)])
                            ->first();

                        if (!$governorate) {
                            Log::warning('Governorate not found for name: ' . $governorateName);

                            try {
                                $governorate = Governorate::create([
                                    'name' => [
                                        'en' => $governorateName,
                                        'ar' => $governorateName, // You can set empty '' if needed
                                    ],
                                ]);
                                Log::info('Created new governorate: ' . $governorateName);
                            } catch (\Exception $e) {
                                Log::error('Failed to create governorate: ' . $governorateName . ' - ' . $e->getMessage());
                                return null;
                            }
                        }

                        return $governorate;
                    }
                )

                ->requiredMapping()
                ->rules(['required']),

            ImportColumn::make('cost')
                ->label('Shipping Cost')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:0']),

            ImportColumn::make('shipping_estimate_time')
                ->label('Shipping Estimate Time')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
        ];
    }

    public function resolveRecord(): ?City
    {
        Log::info('Processing city import row: ', $this->data);
        return new City();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your city import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
