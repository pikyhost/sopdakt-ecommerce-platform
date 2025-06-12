<?php

namespace App\Filament\Resources\WheelPrizeResource\Pages;

use App\Filament\Resources\WheelPrizeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWheelPrizes extends ListRecords
{
    use ListRecords\Concerns\Translatable;

    protected static string $resource = WheelPrizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
