<?php

namespace App\Filament\Resources\WheelResource\Pages;

use App\Filament\Resources\WheelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWheels extends ListRecords
{
    use ListRecords\Concerns\Translatable;

    protected static string $resource = WheelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
