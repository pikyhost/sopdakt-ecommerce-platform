<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false)); // false to avoid double domain prefix

        return (new MailMessage)
            ->subject('Reset Password Notification updated')
            ->line('updated You are receiving this email because we received a password reset request for your account.')
            ->action(' updated Reset Password', $resetUrl)
            ->line('updated This password reset link will expire in 60 minutes.')
            ->line('updated If you did not request a password reset, no further action is required.');
    }
}
