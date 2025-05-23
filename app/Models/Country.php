<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use HasTranslations;

    public $translatable = ['name'];

    protected $guarded = [];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_country');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    public function governorates()
{
    return $this->hasMany(Governorate::class);
}

}
