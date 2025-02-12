<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasColor, HasLabel
{
    case Uncompleted = 'uncompleted';
    case Completed = 'completed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Uncompleted => 'غير مكتمل', // 'Free' in Arabic
            self::Completed => 'مكتمل', // 'Active' in Arabic
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Uncompleted => 'danger',  // Color for 'Free' status (example: gray)
            self::Completed => 'success', // Color for 'Active' status (example: green)
        };
    }
}
