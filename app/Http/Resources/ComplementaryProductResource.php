<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplementaryProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', app()->getLocale()), // localized name
            'slug' => $this->slug,
            'feature_product_image_url' => $this->getFeatureProductImageUrl() ?? '',
            'price' => $this->discount_price_for_current_country ?? 0,
        ];
    }
}
