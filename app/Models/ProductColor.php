<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    protected $fillable = ['product_id', 'color_id', 'image'];

    public $incrementing = false; // No auto-incrementing ID
    protected $primaryKey = null; // Prevent Laravel from expecting a single ID key
    public $timestamps = true;

    // Ensure Laravel does not try to access a single primary key
    public function getKeyName()
    {
        return 'product_id'; // Just return one column to prevent errors
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
