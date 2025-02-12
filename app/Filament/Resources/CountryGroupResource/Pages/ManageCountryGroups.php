<?php

namespace App\Filament\Resources\CountryGroupResource\Pages;

use App\Filament\Resources\CountryGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCountryGroups extends ManageRecords
{
    use ManageRecords\Concerns\Translatable;

    protected static string $resource = CountryGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
