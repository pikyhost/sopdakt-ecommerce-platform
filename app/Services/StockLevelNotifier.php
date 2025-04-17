<?php

// app/Services/StockLevelNotifier.php

namespace App\Services;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class StockLevelNotifier
{
    public static function notifyAdminsForLowStock(Collection $products): void
    {
        $admins = User::role(['admin', 'super_admin'])->get();

        foreach ($products as $product) {
            if (
                $product->quantity !== null &&
                $product->minimum_stock_level !== null &&
                $product->quantity <= $product->minimum_stock_level
            ) {
                foreach ($admins as $admin) {
                    $admin->notify(
                        Notification::make()
                            ->title("⚠️ Low Stock Alert")
                            ->body("Product '{$product->name}' has reached the minimum stock level ({$product->quantity} left).")
                            ->warning()
                            ->toDatabase()
                    );
                }
            }
        }
    }
}
