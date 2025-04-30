<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'phone' => 'required|string|min:11',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:10',
        ];
    }
}
