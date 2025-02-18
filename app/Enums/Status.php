<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    /**
     * Get the translated label for the status.
     */
    public function getLabel(): string
    {
        return __('status.' . $this->value);
    }

    /**
     * Get the color for the status.
     */
    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }

    public static function getLabelFor(string $status): string
    {
        return self::tryFrom($status)?->getLabel() ?? ucfirst((string) $status);
    }

    public static function getColorFor(string $status): string
    {
        return self::tryFrom($status)?->getColor() ?? 'gray';
    }
}
