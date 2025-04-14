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

        // Attempt login
        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        // Check if user can access the panel
        if (($user instanceof FilamentUser) && ! $user->canAccessPanel(Filament::getCurrentPanel())) {
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        // Check if user is inactive
        if (! $user->is_active) {
            Filament::auth()->logout();
            throw ValidationException::withMessages([
                'data.email' => __('Your account is currently blocked.'),
            ]);
        }

        // Normalize and check both phone numbers
        $phones = [
            preg_replace('/[^0-9+]/', '', $user->phone ?? ''),
            preg_replace('/[^0-9+]/', '', $user->second_phone ?? ''),
        ];

        foreach ($phones as $phone) {
            if ($phone && GeneralHelper::isPhoneBlocked($phone)) {
                Filament::auth()->logout();
                throw ValidationException::withMessages([
                    'data.email' => __('You cannot log in using this phone number.'),
                ]);
            }
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

}
