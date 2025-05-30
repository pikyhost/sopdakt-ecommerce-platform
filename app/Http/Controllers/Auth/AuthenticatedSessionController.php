<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Exception;

/**
 * @group Authentication
 *
 * APIs for handling user authentication, including login and logout operations.
 */
class AuthenticatedSessionController extends Controller
{
    public function store(LoginRequest $request)
    {
        try {
            if (Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user();

                return response()->json([
                    'message' => 'User is already logged in',
                    'user' => $user,
                    'role' => $user->getRoleNames()->first(),
                    'token' => $user->currentAccessToken()?->plainTextToken,
                    'redirect_url' => $this->getRedirectUrl($user, $request),
                ], 409);
            }

            $request->authenticate();
            $user = Auth::guard('sanctum')->user();

            Auth::guard('web')->login($user);
            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'role' => $user->getRoleNames()->first(),
                'token' => $token,
                'redirect_url' => $this->getRedirectUrl($user, $request),
            ], 200)->withCookie(cookie('XSRF-TOKEN', csrf_token(), 0, '/', null, true, true, false, 'strict'));

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Authentication failed',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    protected function getRedirectUrl($user, Request $request): string
    {
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return 'https://backend.sopdakt.com/admin';
        }

        if ($user->hasRole('client')) {
            return 'https://backend.sopdakt.com/client'; // relative URL for frontend redirection
        }

        return '/'; // default fallback
    }

    public function destroy(Request $request)
    {
        try {
            // Rate limit logout attempts
            $key = 'logout|' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json([
                    'message' => 'Too many logout attempts. Please try again later.',
                ], 429);
            }

            // Check if user is authenticated via token
            $user = $request->user(); // equivalent to Auth::user() in this context

            if (!$user || !$user->currentAccessToken()) {
                return response()->json([
                    'message' => 'No active session or token found'
                ], 401);
            }

            // Revoke the current access token
            $user->currentAccessToken()->delete();

            // Do NOT call session-related methods for token-based auth
            // This would cause the "Session store not set" error

            // Rate limit hit
            RateLimiter::hit($key, 60); // 60 seconds

            // Return success response
            return response()->json([
                'message' => 'Logout successful',
                'user' => $user->only(['id', 'name', 'email'])
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
