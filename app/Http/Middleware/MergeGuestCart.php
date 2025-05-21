<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MergeGuestCart
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Only merge after successful authentication
        if (Auth::check() && Auth::user()->wasRecentlyCreated === false) {
            $sessionId = $request->header('X-Guest-Session') ?? session()->getId();

            if ($sessionId) {
                app(\App\Services\CartService::class)->mergeGuestCart(
                    Auth::user()->cart ?: Auth::user()->cart()->create(),
                    $sessionId
                );

                // Clear guest session after merge
                if ($request->hasHeader('X-Guest-Session')) {
                    $response->header('Clear-Guest-Session', 'true');
                }
            }
        }

        return $response;
    }
}
