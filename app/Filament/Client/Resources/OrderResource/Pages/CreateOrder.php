<?php

namespace App\Filament\Client\Resources\OrderResource\Pages;

use App\Filament\Client\Resources\OrderResource;
use App\Models\Product;
use App\Services\StockLevelNotifier;
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
