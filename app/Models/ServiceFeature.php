<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ServiceFeature extends Model
{
    use HasTranslations;

    public $translatable = [
        'title',
        'subtitle'
    ]; // Define translatable fields

    protected $fillable = [
        'title',
        'subtitle',
        'icon',
    ];
}
