<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum UserRole: string implements HasColor, HasLabel
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Client = 'client';

    /**
     * Get the translated label for the role.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::SuperAdmin => __('roles.super_admin'),
            self::Admin => __('roles.admin'),
            self::Client => __('roles.client'),
        };
    }

    /**
     * Get the color for the role.
     */
    public function getColor(): string
    {
        return match ($this) {
            self::SuperAdmin => 'danger',
            self::Admin => 'success',
            self::Client => 'info',
        };
    }

    public static function getLabelFor(string $role): string
    {
        return self::tryFrom($role)?->getLabel() ?? Str::headline($role);
    }

    public static function getColorFor(string $role): string
    {
        return self::tryFrom($role)?->getColor() ?? 'warning';
    }
}
