<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Artisan::command('jt-express:sync-statuses', function () {
    $this->call('jt-express:sync-statuses');
})->purpose('Sync order statuses from J&T Express API')
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/schedule.log'));


Artisan::command('order:normalize-shipping-response', function () {
    $this->call('order:normalize-shipping-response');
})->purpose('Sync order statuses from J&T Express API')
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/schedule.log'));

Artisan::command('aramex:check-shipments', function () {
    $this->call('aramex:check-shipments');
})->purpose('Sync order statuses from Aramix API')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/schedule.log'));

Artisan::command('bosta:poll-statuses', function () {
    $this->call('bosta:poll-statuses');
})->purpose('Sync order statuses from Bosta API')
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/schedule.log'));
