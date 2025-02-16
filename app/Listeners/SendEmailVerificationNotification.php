<?php

namespace App\Listeners;

use Filament\Facades\Filament;
use Filament\Notifications\Auth\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class SendEmailVerificationNotification
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        if ($event->user instanceof MustVerifyEmail && ! $event->user->hasVerifiedEmail()) {
            // @TODO Get the user's preferred locale
            $locale = app()->getLocale();

            $notification = new VerifyEmail($locale);
            $notification->url = Filament::getVerifyEmailUrl($event->user);

            $event->user->notify($notification);
        }
    }
}
