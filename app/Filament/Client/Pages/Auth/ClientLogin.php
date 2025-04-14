<?php

namespace App\Filament\Client\Pages\Auth;

use App\Helpers\GeneralHelper;
use Filament\Facades\Filament;
use Filament\Pages\Auth\Login as BasePage;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class ClientLogin extends BasePage
{
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        $data = $this->form->getState();

        $identifier = $data['phone'];

        // Normalize phone (e.g., remove spaces, dashes, etc.)
        $normalized = preg_replace('/[^0-9+]/', '', $identifier);

        if (GeneralHelper::isPhoneBlocked($normalized)) {
            throw ValidationException::withMessages([
                'data.email' => __('This phone number is blocked from logging in.'),
            ]);
        }

        // Proceed with login
        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        } elseif (!$user->is_active) {
            Filament::auth()->logout();
            throw ValidationException::withMessages([
                'data.email' => __('Your account is blocked now.'),
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
