<?php

namespace App\Filament\Resources\GovernorateResource\Pages;

use App\Filament\Resources\GovernorateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGovernorates extends ManageRecords
{
    use ManageRecords\Concerns\Translatable;

    protected static string $resource = GovernorateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
