<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedProduct extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
