<?php

namespace App\Mail;

use App\Models\Invitation;
use App\Models\Setting;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;

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
        $acceptUrl = $this->generateAcceptUrl();

        $this->locale = $this->getBrowserPreferredLanguage();

        $siteSettings = Setting::getAllSettings();

        $siteName = $siteSettings["site_name"] ?? config('app.name');

        return $this->view('emails.guest-invitation')
            ->subject(__('emails.invitation_subject', ['app' => $siteName]))
            ->with([
                'invitation' => $this->invitation,
                'acceptUrl' => $acceptUrl,
            ]);
    }

    /**
     * Generate the signed URL for accepting the invitation.
     */
    private function generateAcceptUrl(): string
    {
        return URL::signedRoute(
            'invitation.accept',
            [
                'invitation' => $this->invitation,
                'name' => request()->input('name'), // Pass contact name
                'phone' => request()->input('phone'), // Pass order phone
            ]
        );
    }

}
