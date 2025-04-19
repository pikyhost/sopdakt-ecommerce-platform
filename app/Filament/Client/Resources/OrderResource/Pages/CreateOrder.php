<?php

namespace App\Filament\Client\Resources\OrderResource\Pages;

use App\Filament\Client\Resources\OrderResource;
use App\Models\Order;
use Filament\Notifications\Notification;
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

    public static function beforeCreate(array $data): void
    {
        if (!empty($data['checkout_token']) && Order::where('checkout_token', $data['checkout_token'])->exists()) {
            $locale = app()->getLocale();

            Notification::make()
                ->title($locale === 'ar' ? 'تم إرسال الطلب مسبقًا' : 'Duplicate Order Submission')
                ->body($locale === 'ar'
                    ? 'هذا الطلب تم تقديمه بالفعل. يرجى الانتظار قليلاً قبل المحاولة مرة أخرى.'
                    : 'This order has already been submitted. Please wait a moment before trying again.')
                ->color('danger')
                ->send();

            return; // Optionally stop further processing
        }
    }


}
