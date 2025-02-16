<?php

namespace App\Filament\Client\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Log;

/**
 * @property Form $form
 */
class EmailVerification extends EmailVerificationPrompt
{
    use WithRateLimiting;

    protected static string $view = 'filament-panels::pages.auth.email-verification.email-verification-prompt';

    public function mount(): void
    {
        if ($this->getVerifiable()->hasVerifiedEmail()) {
            redirect()->intended(Filament::getUrl());
        }
    }

    protected function getVerifiable(): MustVerifyEmail
    {
        /** @var MustVerifyEmail */
        $user = Filament::auth()->user();

        return $user;
    }

    protected function sendEmailVerificationNotification(MustVerifyEmail $user): void
    {
        if ($user->hasVerifiedEmail()) {
            Log::info('User has already verified email.');
            return;
        }

        if (! method_exists($user, 'notify')) {
            Log::error("Model [" . get_class($user) . "] does not have a [notify()] method.");

            throw new Exception("Model [" . get_class($user) . "] does not have a [notify()] method.");
        }

        try {
            Log::info('Sending verification email to: ' . $user->email);

            $notification = app(VerifyEmail::class);
            $notification->url = Filament::getVerifyEmailUrl($user);

            $user->notify($notification);

            Log::info('Verification email sent successfully.');
        } catch (Exception $e) {
            Log::error('Error sending verification email: ' . $e->getMessage());
        }
    }


    public function resendNotificationAction(): Action
    {
        return Action::make('resendNotification')
            ->link()
            ->label(__('filament-panels::pages/auth/email-verification/email-verification-prompt.actions.resend_notification.label') . '.')
            ->action(function (): void {
                try {
                    $this->rateLimit(2);
                } catch (TooManyRequestsException $exception) {
                    $this->getRateLimitedNotification($exception)?->send();

                    return;
                }

                $this->sendEmailVerificationNotification($this->getVerifiable());

                Notification::make()
                    ->title(__('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resent.title'))
                    ->success()
                    ->send();
            });
    }

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        return Notification::make()
            ->title(__('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resend_throttled.title', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]))
            ->body(array_key_exists('body', __('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resend_throttled') ?: []) ? __('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resend_throttled.body', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]) : null)
            ->danger();
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament-panels::pages/auth/email-verification/email-verification-prompt.title');
    }

    public function getHeading(): string | Htmlable
    {
        return __('filament-panels::pages/auth/email-verification/email-verification-prompt.heading');
    }
}
