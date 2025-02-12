<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CountryGroup extends Model
{
    use HasTranslations;

    protected $fillable = ['name'];

    public array $translatable = ['name'];

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'country_group_country');
    }

    public function productSpecialPrices()
    {
        return $this->belongsToMany(ProductSpecialPrice::class, 'product_special_price_country_group');
    }
}
