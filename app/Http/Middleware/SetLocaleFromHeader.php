<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocaleFromHeader
{
    public function handle($request, Closure $next)
    {
        $locale = $request->header('Accept-Language', 'en'); // default to English
        if (in_array($locale, ['en', 'ar'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
