<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $order = $this->record;

        // Convert string status to enum
        $previousStatus = OrderStatus::tryFrom($order->status);
        $newStatus = OrderStatus::tryFrom($data['status']);

        // Check if the order is being moved to "Cancelled" or "Refund" and was previously in a processable state
        if (
            in_array($previousStatus, [OrderStatus::Pending, OrderStatus::Preparing, OrderStatus::Shipping]) &&
            in_array($newStatus, [OrderStatus::Cancelled, OrderStatus::Refund])
        ) {
            // Restore stock when an order is cancelled or refunded
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('quantity', $item->quantity);
                        $product->inventory()->increment('quantity', $item->quantity);
                    }
                }
            }
        }

        return $data;
    }
}
