<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Attribute extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'description', 'type', 'values', 'default_value'];

    public $translatable = ['name', 'description', 'values']; // Enable translation

    protected $casts = [
        'name' => 'array', // Translatable name
        'values' => 'array', // Store select values as JSON
        'default_value' => 'json', // Dynamic default value
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('value');
    }
}
