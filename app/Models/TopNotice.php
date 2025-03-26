<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopNotice extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_en', 'content_ar', 'cta_text_en', 'cta_text_ar', 'cta_url',
        'cta_text_2_en', 'cta_text_2_ar', 'cta_url_2', 'limited_time_text_en',
        'limited_time_text_ar', 'is_active'
    ];
}
