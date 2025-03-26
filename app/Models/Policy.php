<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = [
        'privacy_policy_en', 'privacy_policy_ar',
        'refund_policy_en', 'refund_policy_ar',
        'terms_of_service_en', 'terms_of_service_ar',
    ];

    public static function getPolicy(string $policyType, string $locale = 'en'): ?string
    {
        return static::first()?->{"{$policyType}_{$locale}"};
    }
}
