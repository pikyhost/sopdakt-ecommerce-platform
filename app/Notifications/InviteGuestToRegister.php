<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InviteGuestToRegister extends Notification
{
    protected $contact;

    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Join Us & Track Your Orders!')
            ->greeting('Hello ' . $this->contact->name . ',')
            ->line('We noticed you placed an order as a guest. Create an account to track your orders and get exclusive benefits!')
            ->action('Complete Your Registration', url('/client/register?email=' . $this->contact->email))
            ->line('Thank you for shopping with us!');
    }
}
