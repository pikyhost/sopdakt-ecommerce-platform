<?php

namespace App\Filament\Resources\BlockedPhoneNumberResource\Pages;

use App\Filament\Resources\BlockedPhoneNumberResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBlockedPhoneNumbers extends ManageRecords
{
    use ManageRecords\Concerns\Translatable;

    protected static string $resource = BlockedPhoneNumberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
