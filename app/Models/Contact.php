<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'name',
        'phone',
        'email',
        'country_id',
        'governorate_id',
        'city_id',
        'address',
        'notes',
    ];
}
