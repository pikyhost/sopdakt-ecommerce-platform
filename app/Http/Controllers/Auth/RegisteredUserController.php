<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Governorate;
use App\Models\City;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are already logged in.',
                ], 403);
            }
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string'],
            'second_phone' => ['nullable', 'string'],
            'preferred_language' => ['nullable', 'string', 'max:5'],
            'avatar_url' => ['nullable', 'url'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'governorate_id' => ['nullable', 'exists:governorates,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
        ]);

        if ($request->country_id && $request->governorate_id) {
            $governorateBelongsToCountry = Governorate::where('id', $request->governorate_id)
                ->where('country_id', $request->country_id)
                ->exists();

            if (!$governorateBelongsToCountry) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('auth.invalid_governorate'),
                ], 400);
            }
        }

        if ($request->governorate_id && $request->city_id) {
            $cityBelongsToGovernorate = City::where('id', $request->city_id)
                ->where('governorate_id', $request->governorate_id)
                ->exists();

            if (!$cityBelongsToGovernorate) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('auth.invalid_city'),
                ], 400);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'second_phone' => $request->second_phone,
            'preferred_language' => $request->preferred_language ?? 'en',
            'avatar_url' => $request->avatar_url,
            'country_id' => $request->country_id,
            'governorate_id' => $request->governorate_id,
            'city_id' => $request->city_id,
            'is_active' => true,
        ]);

        $user->assignRole('client');

        event(new Registered($user));

        Auth::login($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => __('auth.registration_success'),
            'user' => $user->only([
                'id', 'name', 'email', 'phone', 'second_phone', 'preferred_language',
                'avatar_url', 'country_id', 'governorate_id', 'city_id', 'is_active', 'created_at'
            ]),
            'token' => $token
        ], 201);
    }
}
