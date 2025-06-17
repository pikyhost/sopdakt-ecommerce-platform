<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLoginToken;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
        ]);

        try {
            $googleUser = \Socialite::driver('google')->stateless()->userFromToken($request->access_token);

            //  Check if user already exists by google_id
            $user = User::where('google_id', $googleUser->id)->first();

            //  If no user with google_id, check by email
            if (!$user) {
                $existingUserWithEmail = User::where('email', $googleUser->email)->first();
                if ($existingUserWithEmail) {
                    return response()->json([
                        'message' => 'This email is already registered. Please login using your original method.',
                        'error' => 'Email already exists in system',
                    ], 409); // Conflict
                }

                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt(uniqid()), // random password
                    'email_verified_at' => now(),
                ]);

                $user->assignRole('client');
            }

            // Clear previous tokens
            $user->tokens()->delete();

            // Generate new token
            $token = $user->createToken('google-login')->plainTextToken;

            // Manually login the user via web guard
            Auth::guard('web')->login($user);

            // Store login token info
            try {
                UserLoginToken::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        'token' => $token,
                        'session_id' => null,
                        'is_login' => true,
                    ]
                );
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Session store not set. Please check your session configuration.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return response()->json([
                'message' => 'Google login successful',
                'user' => $user,
                'role' => $user->getRoleNames()->first(),
                'token' => $token,
                'redirect_url' => $this->getRedirectUrl($user, $request, $token),
            ], 200)->withCookie(cookie('XSRF-TOKEN', csrf_token(), 0, '/', null, true, true, false, 'Strict'));

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Invalid Google token',
                'error' => $e->getMessage(),
            ], 422);
        }
    }


    protected function getRedirectUrl($user, Request $request, $token): string
    {
        if ($user->hasRole('client')) {
            return route('filament.client.auth.login', [
                'token' => encrypt($token, "DEG_FUCK")
            ]);
        }

        return '/';
    }
}
