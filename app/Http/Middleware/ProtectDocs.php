<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ProtectDocs
{
    public function handle(Request $request, Closure $next)
    {
        $username = 'frontend';
        $password = 'Strong-Password-yourStrong-Password-123';

        if ($request->getUser() !== $username || $request->getPassword() !== $password) {
            $headers = ['WWW-Authenticate' => 'Basic'];
            return response('Unauthorized.', 401, $headers);
        }

        return $next($request);
    }
}
