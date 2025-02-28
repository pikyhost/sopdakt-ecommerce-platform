<?php

namespace App\Http\Requests\LandingPage;

use Illuminate\Foundation\Http\FormRequest;

class OrderLandingPageBundleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                      => 'required|string',
            'phone'                     => 'nullable|numeric|digits:11',
            'another_phone'             => 'nullable|numeric|digits:11',
            'address'                   => 'required',
            'governorate_id'            => 'required|exists:governorates,id',
            'region_id'                 => 'nullable|exists:regions,id',
            'bundle_landing_page_id'    => 'required|exists:bundles,id',
            'varieties'                 => 'required|array',
            'varieties.*.color_id'      => 'required|exists:colors,id',
            'varieties.*.size_id'       => 'required|exists:sizes,id',
            'quantity'                  => 'required|integer|min:1',
            'notes'                     => 'nullable|string',
            'shipping_type_id'          => 'nullable|exists:shipping_types,id',
        ];
    }
}
