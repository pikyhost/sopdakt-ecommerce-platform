<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Register a new user.
     *
     * Registers and logs in a new user. Returns the user details.
     *
     * @bodyParam name string required The user's name. Example: John Doe
     * @bodyParam email string required The user's email. Example: john@example.com
     * @bodyParam password string required The user's password. Example: secret123
     * @bodyParam password_confirmation string required Must match password. Example: secret123
     * @bodyParam phone string The user's phone number. Example: +123456789
     * @bodyParam second_phone string The user's second phone number. Example: +987654321
     * @bodyParam preferred_language string The user's preferred language. Example: en
     * @bodyParam avatar_url string URL of avatar. Example: https://example.com/avatar.jpg
     * @bodyParam country_id int The country ID. Example: 1
     * @bodyParam governorate_id int The governorate ID. Example: 2
     * @bodyParam city_id int The city ID. Example: 3
     *
     * @response 201 {
     *   "status": "success",
     *   "message": "Registration successful",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "phone": "+123456789",
     *     "second_phone": "+987654321",
     *     "preferred_language": "en",
     *     "avatar_url": "https://example.com/avatar.jpg",
     *     "country_id": 1,
     *     "governorate_id": 2,
     *     "city_id": 3,
     *     "is_active": true,
     *     "created_at": "2025-04-29T10:00:00.000000Z"
     *   }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string'],
            'preferred_language' => ['nullable', 'string', 'max:5'],
            'avatar_url' => ['nullable', 'url'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'governorate_id' => ['nullable', 'exists:governorates,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
        ]);

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

        event(new Registered($user));

        Auth::login($user);

        // Generate API token using Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'preferred_language' => $user->preferred_language,
                'avatar_url' => $user->avatar_url,
                'country_id' => $user->country_id,
                'governorate_id' => $user->governorate_id,
                'city_id' => $user->city_id,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at,
            ],
            'token' => $token
        ], 201);
    }

}
