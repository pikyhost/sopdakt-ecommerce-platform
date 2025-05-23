<?php

namespace App\Filament\Resources\ProductCouponResource\Pages;

use App\Filament\Resources\ProductCouponResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductCoupons extends ListRecords
{
    protected static string $resource = ProductCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
