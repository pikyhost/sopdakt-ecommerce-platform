<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class PageSuggestion
{
    public static function getAvailablePagePaths(): array
    {
        $routes = collect(Route::getRoutes())
            ->filter(function ($route) {
                return in_array('GET', $route->methods()) &&
                    !str_contains($route->uri(), '{') &&
                    !in_array($route->uri(), ['login', 'logout', 'register']);
            })
            ->map(fn($route) => $route->uri())
            ->unique()
            ->values()
            ->toArray();

        return $routes;
    }
}
