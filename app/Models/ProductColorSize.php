<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColorSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_color_id',
        'size_id',
        'quantity',
    ];

    /**
     * Relationship with ProductColor
     */
    public function productColor()
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }

    /**
     * Relationship with Size
     */
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    /**
     * Relationship with Product through ProductColor
     */
    public function product()
    {
        return $this->hasOneThrough(Product::class, ProductColor::class, 'id', 'id', 'product_color_id', 'product_id');
    }

    /**
     * Relationship with Color through ProductColor
     */
    public function color()
    {
        return $this->hasOneThrough(Color::class, ProductColor::class, 'id', 'id', 'product_color_id', 'color_id');
    }
}
