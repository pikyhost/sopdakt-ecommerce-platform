<?php

namespace App\Notifications;

use Filament\Notifications\Auth\VerifyEmail as FilamentNotification;

class VerifiyEmail extends FilamentNotification
{
    public $locale;
    public string $url;

    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    protected function verificationUrl($notifiable): string
    {
        return $this->url;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }
}

