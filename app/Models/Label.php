<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Label extends Model
{
    use HasTranslations;

    public $translatable = ['title'];

    protected $guarded = [];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'label_product');
    }
}
