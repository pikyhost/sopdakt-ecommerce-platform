<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
   use HasFactory;

   protected $guarded = [];

   public function product()
   {
       return $this->belongsTo(Product::class);
   }

    protected static function booted()
    {
        // When inventory quantity is updated, update the product's quantity
        static::updated(function (Inventory $inventory) {
            if ($inventory->wasChanged('quantity')) {
                Product::where('id', $inventory->product_id)
                    ->update(['quantity' => $inventory->quantity]);
            }
        });
    }

}
