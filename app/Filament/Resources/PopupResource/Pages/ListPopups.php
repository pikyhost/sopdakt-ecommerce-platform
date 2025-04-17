<?php

namespace App\Filament\Resources\PopupResource\Pages;

use App\Filament\Resources\PopupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPopups extends ListRecords
{
    use ListRecords\Concerns\Translatable;

    protected static string $resource = PopupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
