<?php

namespace App\Filament\Resources\GovernorateResource\Pages;

use App\Filament\Resources\GovernorateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGovernorate extends ViewRecord
{
    use ViewRecord\Concerns\Translatable;

    protected static string $resource = GovernorateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\EditAction::make()
        ];
    }
}
