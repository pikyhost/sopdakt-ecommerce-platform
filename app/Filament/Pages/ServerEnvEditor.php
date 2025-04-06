<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Filament\Notifications\Notification;

class ServerEnvEditor extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $title = 'Edit .env Settings';
    protected static string $view = 'filament.pages.env-editor';
    protected static ?string $slug = 'edit-serveer-settings';

    public string $APP_NAME;
    public string $APP_LOCALE;
    public string $GEOIP_IPGEOLOCATION_KEY;
    public string $GEOIP_SERVICE;

    public string $JT_EXPRESS_BASE_URL;
    public string $JT_EXPRESS_API_ACCOUNT;
    public string $JT_EXPRESS_PRIVATE_KEY;
    public string $JT_EXPRESS_CUSTOMER_CODE;
    public string $JT_EXPRESS_PASSWORD;

    public string $ANALYTICS_PROPERTY_ID;
    public string $ANALYTICS_SERVICE_ACCOUNT_JSON;

    public string $MAIL_MAILER;
    public string $MAIL_HOST;
    public string $MAIL_PORT;
    public string $MAIL_USERNAME;
    public string $MAIL_PASSWORD;
    public string $MAIL_ENCRYPTION;
    public string $MAIL_FROM_ADDRESS;
    public string $MAIL_FROM_NAME;

    public function mount(): void
    {
        foreach ($this->getEditableKeys() as $key) {
            $this->{$key} = env($key);
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('General')
                ->schema([
                    Forms\Components\TextInput::make('APP_NAME')->label('Application Name')
                        ->helperText('Displayed throughout the app'),
                    Forms\Components\TextInput::make('APP_LOCALE')->label('App Locale')
                        ->helperText('Example: en, ar, fr'),
                ]),

            Forms\Components\Section::make('GeoIP Service')
                ->schema([
                    Forms\Components\TextInput::make('GEOIP_IPGEOLOCATION_KEY')->label('GeoIP API Key'),
                    Forms\Components\TextInput::make('GEOIP_SERVICE')->label('GeoIP Provider'),
                ]),

            Forms\Components\Section::make('JT Express API')
                ->schema([
                    Forms\Components\TextInput::make('JT_EXPRESS_BASE_URL')->label('Base URL'),
                    Forms\Components\TextInput::make('JT_EXPRESS_API_ACCOUNT')->label('API Account'),
                    Forms\Components\TextInput::make('JT_EXPRESS_PRIVATE_KEY')->label('Private Key'),
                    Forms\Components\TextInput::make('JT_EXPRESS_CUSTOMER_CODE')->label('Customer Code'),
                    Forms\Components\TextInput::make('JT_EXPRESS_PASSWORD')->label('Password'),
                ]),

            Forms\Components\Section::make('Analytics')
                ->schema([
                    Forms\Components\TextInput::make('ANALYTICS_PROPERTY_ID')->label('Property ID'),
                    Forms\Components\TextInput::make('ANALYTICS_SERVICE_ACCOUNT_JSON')->label('Service Account Path'),
                ]),

            Forms\Components\Section::make('Mail Settings')
                ->schema([
                    Forms\Components\TextInput::make('MAIL_MAILER')->label('Mailer'),
                    Forms\Components\TextInput::make('MAIL_HOST')->label('Mail Host'),
                    Forms\Components\TextInput::make('MAIL_PORT')->label('Port')->numeric(),
                    Forms\Components\TextInput::make('MAIL_USERNAME')->label('Username'),
                    Forms\Components\TextInput::make('MAIL_PASSWORD')->label('Password'),
                    Forms\Components\TextInput::make('MAIL_ENCRYPTION')->label('Encryption'),
                    Forms\Components\TextInput::make('MAIL_FROM_ADDRESS')->label('From Address'),
                    Forms\Components\TextInput::make('MAIL_FROM_NAME')->label('From Name'),
                ]),
        ];
    }

    public function save(): void
    {
        try {
            $envPath = base_path('.env');
            $envContent = File::get($envPath);

            foreach ($this->getEditableKeys() as $key) {
                $value = $this->{$key};
                $escapedValue = strpos($value, ' ') !== false || str_contains($value, ';') || str_contains($value, '"')
                    ? '"' . addslashes($value) . '"'
                    : $value;

                // replace line if key exists
                if (preg_match("/^{$key}=.*/m", $envContent)) {
                    $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$escapedValue}", $envContent);
                } else {
                    // add to end if not exists
                    $envContent .= "\n{$key}={$escapedValue}";
                }
            }

            File::put($envPath, $envContent);

            Notification::make()
                ->title('Updated .env successfully')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error updating .env')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getEditableKeys(): array
    {
        return [
            'APP_NAME', 'APP_LOCALE',
            'GEOIP_IPGEOLOCATION_KEY', 'GEOIP_SERVICE',
            'JT_EXPRESS_BASE_URL', 'JT_EXPRESS_API_ACCOUNT',
            'JT_EXPRESS_PRIVATE_KEY', 'JT_EXPRESS_CUSTOMER_CODE',
            'JT_EXPRESS_PASSWORD',
            'ANALYTICS_PROPERTY_ID', 'ANALYTICS_SERVICE_ACCOUNT_JSON',
            'MAIL_MAILER', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME',
            'MAIL_PASSWORD', 'MAIL_ENCRYPTION', 'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME',
        ];
    }
}
