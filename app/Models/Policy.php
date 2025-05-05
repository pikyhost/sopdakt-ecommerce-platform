<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function getPolicy(string $policyType, string $locale = 'en'): ?string
    {
        return static::first()?->{"{$policyType}_{$locale}"};
    }
}
