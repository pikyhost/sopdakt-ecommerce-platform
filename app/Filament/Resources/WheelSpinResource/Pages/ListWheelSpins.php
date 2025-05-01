<?php

namespace App\Filament\Resources\WheelSpinResource\Pages;

use App\Filament\Resources\WheelSpinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWheelSpins extends ListRecords
{
    protected static string $resource = WheelSpinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
