<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Category extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia, Sluggable;

    public $translatable = [
        'name',
        'title_banner_text',
        'cta_banner_text',
        'description',
        'meta_title',
        'meta_description',
    ];

    protected $guarded = [];

    /**
     * Configure the settings for generating slugs.
     */
    public function sluggable(): array
    {
        return [
            'slug' => ['source' => 'title'],
        ];
    }

    /**
     * Get the route key name for Laravel routing.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the subcategories.
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'label_category');
    }


    /**
     * Get the books that belong to this series.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Register the media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_category_image')->singleFile();
    }

    /**
     * Get the URL for the 'main_author_image' .
     */
    public function getMainCategoryImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('main_category_image') ?: null;
    }
}
