<?php

namespace App\Filament\Resources\ShippingTypeResource\Pages;

use App\Filament\Resources\ShippingTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageShippingTypes extends ManageRecords
{
    use ManageRecords\Concerns\Translatable;

    protected static string $resource = ShippingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
