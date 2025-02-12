<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TransactionType: string implements HasColor, HasLabel
{
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case RESTOCK = 'restock';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PURCHASE => __('transaction.purchase'),
            self::SALE => __('transaction.sale'),
            self::RESTOCK => __('transaction.restock'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PURCHASE => 'success',  // Green
            self::SALE => 'danger',      // Red
            self::RESTOCK => 'info',     // Blue
        };
    }
}
