<?php

namespace App\Http\Requests\LandingPage;

use Illuminate\Foundation\Http\FormRequest;

class OrderLandingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => 'required|string',
            'phone'             => 'required|numeric|digits:11',
            'another_phone'     => 'nullable|numeric|digits:11',
            'address'           => 'required|string',
            'governorate_id'    => 'required|exists:governorates,id',
            'region_id'         => 'nullable|exists:regions,id',
            'color_id'          => 'required|exists:colors,id',
            'size_id'           => 'required|exists:sizes,id',
            'quantity'          => 'required|integer|min:1',
            'notes'             => 'nullable|string',
            'shipping_type_id'  => 'nullable|exists:shipping_types,id',
        ];
    }
}
