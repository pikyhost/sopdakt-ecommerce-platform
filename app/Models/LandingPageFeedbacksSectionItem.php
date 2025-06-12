<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageFeedbacksSectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'landing_page_id',
        'image',
        'comment',
        'user_name',
        'user_position',
        'status',
        'order',
        'created_at',
        'updated_at',
    ];
}
