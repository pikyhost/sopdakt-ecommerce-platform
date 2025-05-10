<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel
{
    case Preparing = 'Preparing';
    case Shipping = 'Shipping';
    case Completed = 'Completed';
    case Refund = 'Refund';
    case Cancelled = 'Cancelled';
    case Delayed = 'Delayed';

    public function getLabel(): ?string
    {
        return $this->value;
    }
}
