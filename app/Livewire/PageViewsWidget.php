<?php

namespace App\Livewire;

use BezhanSalleh\FilamentGoogleAnalytics\Widgets\PageViewsWidget as BasePageViewsWidget;

class PageViewsWidget extends BasePageViewsWidget
{
    protected static bool $isLazy = false;
}
