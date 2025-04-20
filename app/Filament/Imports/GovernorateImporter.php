<?php

namespace App\Filament\Imports;

use App\Models\Governorate;
use App\Models\Country;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class GovernorateImporter extends Importer
{
    protected static ?string $model = Governorate::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name_en')
                ->label('Name (English)')
                ->required(),

            ImportColumn::make('name_ar')
                ->label('Name (Arabic)')
                ->required(),

            ImportColumn::make('country_code')
                ->label('Country Code')
                ->helperText('ISO code like EG or SA')
                ->required(),

            ImportColumn::make('cost')
                ->label('Shipping Cost')
                ->numeric()
                ->required(),
        ];
    }

    public function resolveRecord(): ?Governorate
    {
        $country = Country::where('code', $this->data['country_code'])->first();

        if (! $country) {
            $this->fail('Country with code "' . $this->data['country_code'] . '" not found.');
            return null;
        }

        return Governorate::firstOrNew([
            // You can adjust uniqueness check here (e.g., by name + country)
            'name->en' => $this->data['name_en'],
            'country_id' => $country->id,
        ]);
    }

    public function fillRecord(): void
    {
        $country = Country::where('code', $this->data['country_code'])->first();

        $this->record->setTranslations('name', [
            'en' => $this->data['name_en'],
            'ar' => $this->data['name_ar'],
        ]);

        $this->record->cost = $this->data['cost'];
        $this->record->country()->associate($country);
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
