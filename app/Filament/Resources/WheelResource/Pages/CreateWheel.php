<?php

namespace App\Filament\Resources\WheelResource\Pages;

use App\Filament\Resources\WheelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWheel extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = WheelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
