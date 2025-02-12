<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Size extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name']; // Define translatable fields

    protected $guarded = [];
}
