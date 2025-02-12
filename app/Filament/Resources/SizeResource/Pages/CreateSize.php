<?php

namespace App\Filament\Resources\SizeResource\Pages;

use App\Filament\Resources\SizeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSize extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = SizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
