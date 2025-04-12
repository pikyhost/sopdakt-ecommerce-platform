<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Auth\Events\Registered;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    protected function afterCreate(): void
    {
        // Fire the 'Registered' event so Laravel sends the email verification notification
        event(new Registered($this->record));
    }
}
