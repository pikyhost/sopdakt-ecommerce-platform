<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Blog extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia, Sluggable;

    protected $guarded = [];

    public $translatable = ['title', 'content'];

    protected $casts = [
        'published_at' => 'datetime'
    ];

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

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'blog_tag');
    }

    /**
     * Register the media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_blog_image')->singleFile();

    }

    /**
     * Register media conversions for images.
     */
    public function registerMediaConversions(\Spatie\MediaLibrary\Conversions\Conversion|\Spatie\MediaLibrary\MediaCollections\Models\Media|null $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(600)
            ->height(600)
            ->keepOriginalImageFormat()
            ->quality(100)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(1200)
            ->height(1200)
            ->keepOriginalImageFormat()
            ->quality(100)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(2000)
            ->height(2000)
            ->keepOriginalImageFormat()
            ->quality(100)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();
    }

    /**
     * Get the URL for the 'main_author_image' .
     */
    public function getMainBlogImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('main_blog_image', 'thumb') ?: null;
    }

    public function likers()
    {
        return $this->belongsToMany(User::class, 'blog_user_likes')->withTimestamps();
    }

}
