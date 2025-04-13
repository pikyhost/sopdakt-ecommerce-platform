<?php

namespace App\Filament\Client\Resources\ContactMessageResource\Pages;

use App\Filament\Client\Resources\ContactMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageContactMessages extends ManageRecords
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
