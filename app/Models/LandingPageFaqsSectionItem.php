<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageFaqsSectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'landing_page_id',
        'question',
        'answer',
        'status',
        'order',
        'created_at',
        'updated_at',
    ];
}
