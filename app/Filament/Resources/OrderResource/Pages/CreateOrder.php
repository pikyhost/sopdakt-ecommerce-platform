<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Product;
use App\Services\StockLevelNotifier;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

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
