<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Services\StockLevelNotifier;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->after(function (Order $order) {
                // Get the related product IDs from the order items
                $productIds = $order->items
                    ->pluck('product_id')
                    ->filter() // remove nulls (if you allow bundles, etc.)
                    ->unique();

                // Load the updated product records
                $products = Product::whereIn('id', $productIds)->get();

                // Trigger notification if any products are below minimum stock
                StockLevelNotifier::notifyAdminsForLowStock($products);
            }),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make(__('All')),
            'pending' => Tab::make(__('Pending'))->query(fn ($query) => $query->where('status', 'pending')),
            'preparing' => Tab::make(__('Preparing'))->query(fn ($query) => $query->where('status', 'preparing')),
            'shipping' => Tab::make(__('Shipping'))->query(fn ($query) => $query->where('status', 'shipping')),
            'delayed' => Tab::make(__('Delayed'))->query(fn ($query) => $query->where('status', 'delayed')),
            'refund' => Tab::make(__('Refund'))->query(fn ($query) => $query->where('status', 'refund')),
            'cancelled' => Tab::make(__('Cancelled'))->query(fn ($query) => $query->where('status', 'cancelled')),
            'completed' => Tab::make(__('Completed'))->query(fn ($query) => $query->where('status', 'completed')),
        ];
    }
}
