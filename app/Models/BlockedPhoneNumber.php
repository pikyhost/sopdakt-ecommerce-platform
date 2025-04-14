<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class BlockedPhoneNumber extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = ['phone_number', 'reason'];

    public $translatable = ['reason'];
}
