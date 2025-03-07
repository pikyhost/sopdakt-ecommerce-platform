<?php

namespace App\Filament\Resources\LandingPageOrderResource\Pages;

use App\Filament\Resources\LandingPageOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandingPageOrder extends EditRecord
{
    protected static string $resource = LandingPageOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
