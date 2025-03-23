<?php

namespace App\Mail;

use App\Models\Invitation;
use App\Models\Setting;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class GuestInvitationMail extends Mailable
{
    private Invitation $invitation;
    public $locale;

    /**
     * Create a new message instance.
     */
    public function __construct(Invitation $invitation, ?string $locale = null)
    {
        $this->invitation = $invitation;
        $this->locale = $locale ?? app()->getLocale(); // Use app locale if none is provided
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $acceptUrl = $this->generateAcceptUrl();

        $siteSettings = Setting::getAllSettings() ?? [];
        $siteName = $siteSettings["site_name"] ?? config('app.name');

        return $this->view('emails.guest-invitation')
            ->subject(__('emails.invitation_subject', ['app' => $siteName], $this->locale))
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
            ['invitation' => $this->invitation]
        );
    }
}
