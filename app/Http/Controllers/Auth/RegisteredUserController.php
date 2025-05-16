<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Governorate;
use App\Models\City;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;

class RegisteredUserController extends Controller
{
    public function store(RegisterRequest $request): JsonResponse
    {
        // Validation is handled by RegisterRequest
        $validated = $request->validated();

        // Additional validation for governorate and city relationships
        if ($validated['country_id'] && $validated['governorate_id']) {
            $governorateBelongsToCountry = Governorate::where('id', $validated['governorate_id'])
                ->where('country_id', $validated['country_id'])
                ->exists();

            if (!$governorateBelongsToCountry) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('auth.invalid_governorate'),
                ], 400);
            }
        }

        if ($validated['governorate_id'] && $validated['city_id']) {
            $cityBelongsToGovernorate = City::where('id', $validated['city_id'])
                ->where('governorate_id', $validated['governorate_id'])
                ->exists();

            if (!$cityBelongsToGovernorate) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('auth.invalid_city'),
                ], 400);
            }
        }

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'second_phone' => $validated['second_phone'],
            'preferred_language' => $validated['preferred_language'] ?? 'en',
            'avatar_url' => $validated['avatar_url'],
            'country_id' => $validated['country_id'],
            'governorate_id' => $validated['governorate_id'],
            'city_id' => $validated['city_id'],
            'is_active' => true,
        ]);

        // Trigger the Registered event
        event(new Registered($user));

        // Generate an API token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => __('auth.registration_success'),
            'user' => $user->only([
                'id', 'name', 'email', 'phone', 'second_phone', 'preferred_language',
                'avatar_url', 'country_id', 'governorate_id', 'city_id', 'is_active', 'created_at'
            ]),
            'token' => $token,
            'redirect_to' => '/dashboard' // Optional: Suggest frontend route
        ], 201);
    }
}
