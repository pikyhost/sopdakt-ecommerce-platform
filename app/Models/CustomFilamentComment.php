<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Parallax\FilamentComments\Models\FilamentComment;

class CustomFilamentComment extends FilamentComment
{
    use HasFactory, SoftDeletes;

    protected $table = 'filament_comments';

    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'comment',
         'status'
    ];
}
