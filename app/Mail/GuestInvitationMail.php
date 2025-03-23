<?php

namespace App\Mail;

use App\Models\Invitation;
use App\Models\Setting;
use Illuminate\Mail\Mailable;

class GuestInvitationMail extends Mailable
{
    private $invitation;
    public $locale;

    /**
     * Create a new message instance.
     */
    public function __construct(Invitation $invitation, string $locale = 'en')
    {
        $this->invitation = $invitation;
        $this->locale = $locale;
    }

    protected function getBrowserPreferredLanguage(): string
    {
        $preferredLanguages = request()->getPreferredLanguage(['en', 'ar']);
        return $preferredLanguages ?: 'en'; // Default to English if no match found
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $this->locale = $this->getBrowserPreferredLanguage();

        $siteSettings = Setting::getAllSettings();

        $siteName = $siteSettings["site_name"] ?? config('app.name');

        return $this->view('emails.guest-invitation')
            ->subject(__('emails.invitation_subject', ['app' => $siteName]))
            ->with([
                'invitation' => $this->invitation,
                'acceptUrl' => url('/client/register?email=' . $this->invitation->email),
            ]);
    }
}
