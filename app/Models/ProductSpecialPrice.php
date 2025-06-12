<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ProductSpecialPrice extends Model
{
    use HasTranslations;

    protected $fillable = ['product_id', 'country_id', 'country_group_id', 'special_price', 'currency_id', 'special_price_after_discount'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function countryGroup(): BelongsTo
    {
        return $this->belongsTo(CountryGroup::class);
    }

    public function countryGroups()
    {
        return $this->belongsToMany(CountryGroup::class, 'product_special_price_country_group');
    }

}
