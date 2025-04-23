<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class SizeGuide extends Model
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    public $translatable = [
        'title',
        'description',
    ];

    protected $guarded = [];
}
