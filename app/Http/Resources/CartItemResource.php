<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $priceString = $this->product ? $this->product->discount_price_for_current_country : '0 USD';
        $price = (float) preg_replace('/[^0-9.]/', '', $priceString);
        $currency = preg_replace('/[\d.]/', '', trim($priceString));

        // Use image from productColor (inferred by product_id + color_id), fallback to product feature image
        $imageUrl = $this->productColor && $this->productColor->image
            ? asset('storage/' . $this->productColor->image)
            : ($this->product?->getFeatureProductImageUrl() ?? '');

        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price_per_unit' => $price,
            'subtotal' => number_format($price * $this->quantity, 2) . ' ' . $currency,
            'product' => $this->product ? [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'slug' => $this->product->slug,
                'feature_product_image_url' => $imageUrl,
                'price' => $this->product->discount_price_for_current_country ?? 0,
            ] : null,
            'bundle' => $this->bundle ? [
                'id' => $this->bundle->id,
                'name' => $this->bundle->name,
                'price' => $this->bundle->discount_price ?? 0,
            ] : null,
            'size' => $this->size ? [
                'id' => $this->size->id,
                'name' => $this->size->name,
            ] : null,
            'color' => $this->color ? [
                'id' => $this->color->id,
                'name' => $this->color->name,
                'code' => $this->color->code,
            ] : null,
        ];
    }
}
