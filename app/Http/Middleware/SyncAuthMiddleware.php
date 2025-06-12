<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SyncAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // If API token is present, log in the user in the session
        if ($request->bearerToken() && Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            Auth::guard('web')->login($user);
        }

        return $next($request);
    }
}
