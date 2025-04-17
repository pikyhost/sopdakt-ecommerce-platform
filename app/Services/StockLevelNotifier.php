<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Filament\Notifications\Actions\Action;

class StockLevelNotifier
{
    public static function notifyAdminsForLowStock(Collection $products): void
    {
        $admins = User::role(['admin', 'super_admin'])->get();

        foreach ($products as $product) {
            if (
                $product->quantity !== null &&
                Setting::getMinimumStockLevel() !== null &&
                $product->quantity <= Setting::getMinimumStockLevel()
            ) {
                foreach ($admins as $admin) {
                    App::setLocale($admin->locale ?? config('app.locale'));
                    $inventory = $product->inventory;

                    Notification::make()
                        ->title(__('notifications.low_stock.title'))
                        ->warning()
                        ->body(__('notifications.low_stock.body', [
                            'name' => $product->name,
                            'quantity' => $product->quantity,
                        ]))
                        ->actions([
                            Action::make('view')
                                ->label(__('notifications.low_stock.view_button'))
                                ->url(route('filament.admin.resources.inventories.view', ['record' => $inventory->id]))
                                ->markAsRead(),
                        ])
                        ->sendToDatabase($admin);
                }
            }
        }
    }
}
