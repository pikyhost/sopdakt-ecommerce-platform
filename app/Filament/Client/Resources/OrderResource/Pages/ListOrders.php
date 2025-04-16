<?php

namespace App\Filament\Client\Resources\OrderResource\Pages;

use App\Filament\Client\Resources\OrderResource;
use App\Models\Order;
use App\Notifications\ProductStockNotifier;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->after(function (Order $record) {
                    ProductStockNotifier::notify($record);
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
