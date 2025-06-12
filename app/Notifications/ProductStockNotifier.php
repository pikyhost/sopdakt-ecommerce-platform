<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\App;

class ProductStockNotifier
{
    public static function notify(Order $order): void
    {
        $adminUsers = User::role(['admin', 'super_admin'])->get();

        foreach ($order->items as $item) {
            $product = $item->product;

            if (! $product || is_null($product->minimum_stock_level)) {
                continue;
            }

            if ($product->quantity <= $product->minimum_stock_level) {
                foreach ($adminUsers as $admin) {
                    App::setLocale($admin->locale ?? config('app.locale'));

                    Notification::make()
                        ->title(__('notifications.low_stock.title'))
                        ->warning()
                        ->body(__('notifications.low_stock.body', [
                            'product' => $product->name,
                            'quantity' => $product->quantity,
                        ]))
                        ->actions([
                            Action::make('view')
                                ->label(__('notifications.low_stock.view_button'))
                                ->url(route('filament.admin.resources.products.view', ['record' => $product->id]))
                                ->markAsRead(),
                        ])
                        ->sendToDatabase($admin);
                }
            }
        }
    }
}
