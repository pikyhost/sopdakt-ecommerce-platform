<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_color_sizes');
    }

    public function productColorSizes()
    {
        return $this->hasMany(ProductColorSize::class);
    }
}
