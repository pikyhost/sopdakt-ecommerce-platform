<?php

namespace App\Filament\Resources\ColorResource\Pages;

use App\Filament\Resources\ColorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateColor extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = ColorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
