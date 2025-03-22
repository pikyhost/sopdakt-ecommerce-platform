<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;

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

    /**
     * Build the message.
     */
    public function build()
    {
        // Set the application locale
        App::setLocale($this->locale);

        $acceptUrl = $this->generateAcceptUrl();

        return $this->view('emails.guest-invitation')
            ->subject(__('emails.invitation_subject', ['app' => config('app.name')]))
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
            'guest.invitation.accept',
            ['invitation' => $this->invitation->id]
        );
    }
}
