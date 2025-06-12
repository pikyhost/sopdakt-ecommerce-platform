<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'user_id' => $this->user_id,
            'session_id' => $this->session_id,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'tax_percentage' => $this->tax_percentage,
            'tax_amount' => $this->tax_amount,
            'shipping_cost' => $this->shipping_cost,
            'country_id' => $this->country_id,
            'governorate_id' => $this->governorate_id,
            'city_id' => $this->city_id,
            'shipping_type_id' => $this->shipping_type_id,
        ];
    }
}
