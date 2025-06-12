<?php

namespace App\Filament\Client\Resources\OrderResource\Pages;

use App\Filament\Client\Resources\OrderResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Filament::auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $this->record->syncInventoryOnCreate();
    }

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();

        if (!empty($data['checkout_token']) && \App\Models\Order::where('checkout_token', $data['checkout_token'])->exists()) {
            $this->halt();
        }
    }
}
