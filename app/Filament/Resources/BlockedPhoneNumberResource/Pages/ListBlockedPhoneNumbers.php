<?php

namespace App\Filament\Resources\BlockedPhoneNumberResource\Pages;

use App\Filament\Resources\BlockedPhoneNumberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlockedPhoneNumbers extends ListRecords
{
    protected static string $resource = BlockedPhoneNumberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
