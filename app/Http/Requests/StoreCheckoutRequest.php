<?php

namespace App\Http\Requests;

use App\Helpers\GeneralHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreCheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Allow all users (authenticated or guests) to make this request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(Auth::id()),
            ],
            'phone' => [
                'required',
                'string',
                'min:10',
                function ($attribute, $value, $fail) {
                    foreach ($this->generatePhoneVariations($value) as $variation) {
                        if (GeneralHelper::isPhoneBlocked($variation)) {
                            $fail('The phone number is blocked. Please contact support at ' . route('contact.us') . '.');
                            break;
                        }
                    }
                },
            ],
            'second_phone' => [
                'required',
                'string',
                'min:10',
                'different:phone',
                function ($attribute, $value, $fail) {
                    foreach ($this->generatePhoneVariations($value) as $variation) {
                        if (GeneralHelper::isPhoneBlocked($variation)) {
                            $fail('The second phone number is blocked. Please contact support at ' . route('contact.us') . '.');
                            break;
                        }
                    }
                },
            ],
            'notes' => ['nullable', 'string'],
            'create_account' => ['boolean'],
            'password' => ['nullable', 'min:6', 'required_if:create_account,true'],
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
            'payment_method_id.required' => 'Please select a payment method.',
            'payment_method_id.exists' => 'The selected payment method is invalid.',
            'name.required' => 'Your name is required.',
            'name.max' => 'Your name cannot exceed 255 characters.',
            'address.required' => 'Your address is required.',
            'address.max' => 'Your address cannot exceed 500 characters.',
            'email.required' => 'Your email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Your email cannot exceed 255 characters.',
            'email.unique' => 'This email is already in use by another account.',
            'phone.required' => 'Your phone number is required.',
            'phone.min' => 'Your phone number must be at least 10 characters.',
            'second_phone.required' => 'A second phone number is required.',
            'second_phone.min' => 'Your second phone number must be at least 10 characters.',
            'second_phone.different' => 'The second phone number must be different from the primary phone number.',
            'password.min' => 'Your password must be at least 6 characters.',
            'password.required_if' => 'A password is required when creating an account.',
        ];
    }

    /**
     * Generate phone number variations for validation.
     *
     * @param string $input
     * @return array
     */
    private function generatePhoneVariations(string $input): array
    {
        $digits = preg_replace('/\D/', '', $input); // Remove non-digits

        if (str_starts_with($digits, '0')) {
            $local = $digits;
            $withoutZero = substr($digits, 1);
        } elseif (str_starts_with($digits, '20')) {
            $local = '0' . substr($digits, 2);
            $withoutZero = substr($digits, 2);
        } else {
            $local = '0' . $digits;
            $withoutZero = $digits;
        }

        return array_unique([
            $local,                        // e.g., 01025263865
            '20' . $withoutZero,          // e.g., 201025263865
            '+20' . $withoutZero,         // e.g., +201025263865
        ]);
    }
}
