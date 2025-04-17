<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Product;
use App\Services\StockLevelNotifier;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $this->record->syncInventoryOnCreate();

        // Get the related product IDs from the order items
        $productIds = $this->record->items
            ->pluck('product_id')
            ->filter() // remove nulls (if you allow bundles, etc.)
            ->unique();

        // Load the updated product records
        $products = Product::whereIn('id', $productIds)->get();

        // Trigger notification if any products are below minimum stock
        StockLevelNotifier::notifyAdminsForLowStock($products);
    }
}
