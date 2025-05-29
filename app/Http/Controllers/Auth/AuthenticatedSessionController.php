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
    /**
     * Handle an incoming authentication request
     *
     * Authenticates a user with the provided credentials. If successful, regenerates the session and returns user data. Returns an error if the user is already logged in or if credentials are invalid.
     *
     * @bodyParam email string required The user's email address. Example: user@example.com
     * @bodyParam password string required The user's password. Example: password123
     * @response 200 {
     *   "message": "Login successful",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "user@example.com"
     *   }
     * }
     * @response 409 {
     *   "message": "User is already logged in",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "user@example.com"
     *   }
     * }
     * @response 422 {
     *   "message": "Validation failed",
     *   "errors": {
     *     "email": ["The email field is required."],
     *     "password": ["The password field is required."]
     *   }
     * }
     * @response 401 {
     *   "message": "Authentication failed",
     *   "error": "Invalid credentials"
     * }
     */
    public function store(LoginRequest $request)
    {
        try {
            // Check if user is already authenticated
            if (Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user();

                return response()->json([
                    'message' => 'User is already logged in',
                    'user' => $user->load('roles'), // Include roles
                    'token' => $user->currentAccessToken()?->plainTextToken,
                ], 409);
            }

            // Attempt authentication
            $request->authenticate();

            $user = Auth::guard('sanctum')->user();

            // Revoke existing tokens if enforcing single session
            $user->tokens()->delete();

            // Create new token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user->load('roles'), // Include roles
                'token' => $token,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Authentication failed',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * Destroy an authenticated session
     *
     * Logs out the authenticated Rectangular user, invalidates the session, and regenerates the CSRF token. Returns an error if no active session is found or if logout attempts are rate-limited.
     *
     * @authenticated
     * @response 200 {
     *   "message": "Logout successful",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "user@example.com"
     *   }
     * }
     * @response 401 {
     *   "message": "No active session found"
     * }
     * @response 429 {
     *   "message": "Too many logout attempts. Please try again later."
     * }
     * @response 500 {
     *   "message": "Logout failed",
     *   "error": "An unexpected error occurred"
     * }
     */

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
