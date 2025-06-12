<?php

namespace App\Mail;

use App\Models\Invitation;
use App\Models\Setting;
use Illuminate\Mail\Mailable;

class GuestInvitationMail extends Mailable
{
    private $invitation;

    /**
     * Create a new message instance.
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $siteSettings = Setting::getAllSettings();

        $siteName = $siteSettings["site_name"] ?? config('app.name');

        return $this->view('emails.guest-invitation')
            ->subject(__('emails.invitation_subject', ['app' => $siteName]))
            ->with([
                'invitation' => $this->invitation,
                'acceptUrl' => route('guest.invitation.accept',  ['invitation' => $this->invitation]),
            ]);
    }
}
