<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id', 'country_id', 'governorate_id', 'city_id', 'address', 'address_name', 'is_primary'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

}
