<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ContactMessageMailNotification extends Notification
{
    public ContactMessage $message;

    public function __construct(ContactMessage $message)
    {
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Contact Message')
            ->greeting('Hello!')
            ->line('A new contact message has been submitted:')
            ->line('**Name:** ' . $this->message->name)
            ->line('**Email:** ' . $this->message->email)
            ->line('**Phone:** ' . $this->message->phone)
            ->line('**Subject:** ' . ($this->message->subject ?? 'N/A'))
            ->line('**Message:**')
            ->line($this->message->message)
            ->line('IP Address: ' . $this->message->ip_address);
    }
}
