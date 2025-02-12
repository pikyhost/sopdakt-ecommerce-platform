<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public function zone()
    {
        return $this->hasOneThrough(Zone::class, ZoneGovernorate::class, 'governorate_id', 'id', 'id', 'zone_id');
    }
}
