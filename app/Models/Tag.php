<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Tag extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function blogs()
    {
        return $this->belongsToMany(Blog::class, 'blog_tag');
    }
}
