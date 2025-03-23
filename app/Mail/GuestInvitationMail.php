<?php

namespace App\Mail;

use App\Models\Invitation;
use App\Models\Setting;
use Illuminate\Mail\Mailable;

class GuestInvitationMail extends Mailable
{
    private Invitation $invitation;
    public  $locale;
    public string $acceptUrl; // Store URL

    public function __construct(Invitation $invitation,  $locale = 'en', string $acceptUrl)
    {
        $this->invitation = $invitation;
        $this->locale = $locale;
        $this->acceptUrl = $acceptUrl;
    }

    public function build()
    {
        $siteSettings = Setting::getAllSettings();
        $siteName = $siteSettings["site_name"] ?? config('app.name');

        return $this->view('emails.guest-invitation')
            ->subject(__('emails.invitation_subject', ['app' => $siteName]))
            ->with([
                'invitation' => $this->invitation,
                'acceptUrl' => $this->acceptUrl,
            ]);
    }
}
