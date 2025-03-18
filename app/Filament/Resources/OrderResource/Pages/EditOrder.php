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

        // Since $order->status is already an enum, use it directly
        $previousStatus = $order->status;

        // Convert the new status string from the form data into an enum
        $newStatus = OrderStatus::tryFrom($data['status']);

        if (
            in_array($previousStatus, [OrderStatus::Pending, OrderStatus::Preparing, OrderStatus::Shipping], true) &&
            in_array($newStatus, [OrderStatus::Cancelled, OrderStatus::Refund], true)
        ) {
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
