<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check header or query parameter for the API key
        $apiKey = $request->header('x-api-key') ?? $request->query('api_key');

        if ($apiKey !== config('api.key')) {
            return response()->json(['message' => 'غير مصرح بالوصول.'], 401);
        }

        return $next($request);
    }

}
