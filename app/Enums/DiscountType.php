<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DiscountType: string implements HasColor, HasLabel
{
    case Percent = 'percent';
    case Fixed = 'fixed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Percent => 'نسبة مئوية', // Arabic label for Salon
            self::Fixed => 'مبلغ ثابت', // Arabic label for Tailor
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Percent => 'success',
            self::Fixed => 'info',
        };
    }
}
