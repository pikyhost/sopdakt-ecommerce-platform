<?php

namespace App\Http\Requests\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class CalculateShippingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'region_id'         => 'required|exists:regions,id',
            'landing_page_id'   => 'required|exists:landing_pages,id',
            'shipping_type_id'  => 'nullable|exists:shipping_types,id',
        ];
    }
}
