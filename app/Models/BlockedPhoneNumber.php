<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedPhoneNumber extends Model
{
    protected $fillable = ['phone_number', 'reason'];
}
