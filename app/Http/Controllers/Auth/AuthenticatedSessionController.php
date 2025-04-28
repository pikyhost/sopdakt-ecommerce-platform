<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Exception;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        try {
            // Check if user is already authenticated
            if (Auth::check()) {
                return response()->json([
                    'message' => 'User is already logged in',
                    'user' => Auth::user()
                ], 409);
            }

            // Attempt authentication
            $request->authenticate();

            // Regenerate session
            $request->session()->regenerate();

            // Return success response with user data
            return response()->json([
                'message' => 'Login successful',
                'user' => Auth::user()
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
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        try {
            // Rate limit logout attempts (optional)
            $key = 'logout|' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json([
                    'message' => 'Too many logout attempts. Please try again later.',
                ], 429);
            }

            // Check if user is authenticated
            if (!Auth::check()) {
                return response()->json([
                    'message' => 'No active session found'
                ], 401);
            }

            // Store user data before logout
            $user = Auth::user();

            // Perform logout
            Auth::guard('web')->logout();

            // Invalidate session
            $request->session()->invalidate();

            // Regenerate CSRF token
            $request->session()->regenerateToken();

            // Increment rate limiter
            RateLimiter::hit($key, 60); // 60 seconds decay

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
