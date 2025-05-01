<?php

namespace App\Http\Requests;

use App\Models\Wheel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpinWheelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Allow all users (authenticated or guests)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'wheel_id' => [
                'required',
                'integer',
                Rule::exists(Wheel::class, 'id')->where(function ($query) {
                    $query->where('is_active', true)
                        ->where(function ($q) {
                            $q->whereNull('start_date')->orWhere('start_date', '<=', now());
                        })
                        ->where(function ($q) {
                            $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                        });
                }),
            ],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'wheel_id.required' => 'The wheel ID is required.',
            'wheel_id.integer' => 'The wheel ID must be an integer.',
            'wheel_id.exists' => 'The selected wheel is not active or does not exist.',
        ];
    }
}
