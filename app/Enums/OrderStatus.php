<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Preparing = 'preparing';
    case Shipping = 'shipping';
    case Delayed = 'delayed';
    case Refund = 'refund';
    case Completed = 'completed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Preparing => __('Preparing'),
            self::Shipping => __('Shipping'),
            self::Delayed => __('Delayed'),
            self::Refund => __('Refund'),
            self::Completed => __('Completed'),
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Pending => 'info',
            self::Preparing => 'warning',
            self::Shipping => 'primary',
            self::Delayed => 'danger',
            self::Refund => 'gray',
            self::Completed => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-m-clock',
            self::Preparing => 'heroicon-m-cog',
            self::Shipping => 'heroicon-m-truck',
            self::Delayed => 'heroicon-m-exclamation-circle',
            self::Refund => 'heroicon-m-arrow-uturn-left',
            self::Completed => 'heroicon-m-check-badge',
        };
    }
}

