<?php

namespace App\Filament\Resources\WheelSpinResource\Pages;

use App\Filament\Resources\WheelSpinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWheelSpins extends ListRecords
{
    use ListRecords\Concerns\Translatable;

    protected static string $resource = WheelSpinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
