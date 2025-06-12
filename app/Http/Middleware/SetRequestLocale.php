<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetRequestLocale
{
    protected array $availableLocales = ['en', 'ar'];

    public function handle($request, Closure $next)
    {
        // Get values from query param and header
        $queryLocale = $request->query('lang');
        $headerLocale = $request->header('Accept-Language');

        // Decide priority: query param > header
        $locale = $queryLocale ?? $headerLocale ?? 'en';

        // Sanitize and apply locale
        if (in_array($locale, $this->availableLocales)) {
            App::setLocale($locale);
        } else {
            App::setLocale('en');
        }

        return $next($request);
    }
}
