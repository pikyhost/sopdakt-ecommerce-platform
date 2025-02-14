<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum BundleType: string implements HasColor, HasLabel
{
    case FIXED_PRICE = 'fixed_price';
    case DISCOUNT_PERCENTAGE = 'discount_percentage';
    case BUY_X_GET_Y = 'buy_x_get_y';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FIXED_PRICE => __('bundles.fixed_price'),
            self::DISCOUNT_PERCENTAGE => __('bundles.discount_percentage'),
            self::BUY_X_GET_Y => __('bundles.buy_x_get_y'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::FIXED_PRICE => 'success',
            self::DISCOUNT_PERCENTAGE => 'danger',
            self::BUY_X_GET_Y => 'warning',
        };
    }
}
