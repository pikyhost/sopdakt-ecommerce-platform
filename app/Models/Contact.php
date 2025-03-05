<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'country_id',
        'governorate_id',
        'session_id',
        'city_id',
        'address',
        'apartment',
        'postcode',
        'email',
        'phone',
        'notes',
        'session_id',
    ];
}
