<?php

namespace App\Filament\Resources\BlockedPhoneNumberResource\Pages;

use App\Filament\Resources\BlockedPhoneNumberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlockedPhoneNumber extends EditRecord
{
    protected static string $resource = BlockedPhoneNumberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
