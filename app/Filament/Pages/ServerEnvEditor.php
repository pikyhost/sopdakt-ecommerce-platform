<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Filament\Notifications\Notification;

class ServerEnvEditor extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $title = 'Server Settings';
    protected static string $view = 'filament.pages.env-editor';
    protected static ?string $slug = 'edit-server-settings';

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

    public static function getNavigationGroup(): ?string
    {
        return __('Settings Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Server Settings');
    }

    public static function getLabel(): ?string
    {
        return __('Server Settings');
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make(__('env.general'))
                ->schema([
                    Forms\Components\TextInput::make('APP_NAME')
                        ->label(__('env.APP_NAME.label'))
                        ->helperText(__('env.APP_NAME.helper'))
                        ->afterStateUpdated(fn ($state, callable $set) => $set('APP_NAME', str_contains($state, ' ') ? "\"{$state}\"" : $state)),

                    Forms\Components\Select::make('APP_LOCALE')
                        ->label(__('env.APP_LOCALE.label'))
                        ->helperText(__('env.APP_LOCALE.helper'))
                        ->options([
                            'en' => __('env.APP_LOCALE.options.en'),
                            'ar' => __('env.APP_LOCALE.options.ar'),
                        ]),
                ]),

            Forms\Components\Section::make(__('env.geoip'))
                ->schema([
                    Forms\Components\TextInput::make('GEOIP_IPGEOLOCATION_KEY')
                        ->label(__('env.GEOIP_IPGEOLOCATION_KEY.label'))
                        ->helperText(__('env.GEOIP_IPGEOLOCATION_KEY.helper')),

                    Forms\Components\TextInput::make('GEOIP_SERVICE')
                        ->label(__('env.GEOIP_SERVICE.label'))
                        ->helperText(__('env.GEOIP_SERVICE.helper')),
                ]),

            Forms\Components\Section::make(__('env.jt_express'))
                ->schema([
                    Forms\Components\TextInput::make('JT_EXPRESS_BASE_URL')
                        ->label(__('env.JT_EXPRESS_BASE_URL.label'))
                        ->helperText(__('env.JT_EXPRESS_BASE_URL.helper')),

                    Forms\Components\TextInput::make('JT_EXPRESS_API_ACCOUNT')
                        ->label(__('env.JT_EXPRESS_API_ACCOUNT.label'))
                        ->helperText(__('env.JT_EXPRESS_API_ACCOUNT.helper')),

                    Forms\Components\TextInput::make('JT_EXPRESS_PRIVATE_KEY')
                        ->label(__('env.JT_EXPRESS_PRIVATE_KEY.label'))
                        ->helperText(__('env.JT_EXPRESS_PRIVATE_KEY.helper')),

                    Forms\Components\TextInput::make('JT_EXPRESS_CUSTOMER_CODE')
                        ->label(__('env.JT_EXPRESS_CUSTOMER_CODE.label'))
                        ->helperText(__('env.JT_EXPRESS_CUSTOMER_CODE.helper')),

                    Forms\Components\TextInput::make('JT_EXPRESS_PASSWORD')
                        ->label(__('env.JT_EXPRESS_PASSWORD.label'))
                        ->helperText(__('env.JT_EXPRESS_PASSWORD.helper')),
                ]),

            Forms\Components\Section::make(__('env.analytics'))
                ->schema([
                    Forms\Components\TextInput::make('ANALYTICS_PROPERTY_ID')
                        ->label(__('env.ANALYTICS_PROPERTY_ID.label'))
                        ->helperText(__('env.ANALYTICS_PROPERTY_ID.helper')),

                    Forms\Components\TextInput::make('ANALYTICS_SERVICE_ACCOUNT_JSON')
                        ->label(__('env.ANALYTICS_SERVICE_ACCOUNT_JSON.label'))
                        ->helperText(__('env.ANALYTICS_SERVICE_ACCOUNT_JSON.helper')),
                ]),

            Forms\Components\Section::make(__('env.mail'))
                ->schema([
                    Forms\Components\TextInput::make('MAIL_MAILER')
                        ->label(__('env.MAIL_MAILER.label'))
                        ->helperText(__('env.MAIL_MAILER.helper')),

                    Forms\Components\TextInput::make('MAIL_HOST')
                        ->label(__('env.MAIL_HOST.label'))
                        ->helperText(__('env.MAIL_HOST.helper')),

                    Forms\Components\TextInput::make('MAIL_PORT')
                        ->label(__('env.MAIL_PORT.label'))
                        ->numeric()
                        ->helperText(__('env.MAIL_PORT.helper')),

                    Forms\Components\TextInput::make('MAIL_USERNAME')
                        ->label(__('env.MAIL_USERNAME.label'))
                        ->helperText(__('env.MAIL_USERNAME.helper')),

                    Forms\Components\TextInput::make('MAIL_PASSWORD')
                        ->label(__('env.MAIL_PASSWORD.label'))
                        ->helperText(__('env.MAIL_PASSWORD.helper')),

                    Forms\Components\TextInput::make('MAIL_ENCRYPTION')
                        ->label(__('env.MAIL_ENCRYPTION.label'))
                        ->helperText(__('env.MAIL_ENCRYPTION.helper')),

                    Forms\Components\TextInput::make('MAIL_FROM_ADDRESS')
                        ->label(__('env.MAIL_FROM_ADDRESS.label'))
                        ->helperText(__('env.MAIL_FROM_ADDRESS.helper')),

                    Forms\Components\TextInput::make('MAIL_FROM_NAME')
                        ->label(__('env.MAIL_FROM_NAME.label'))
                        ->helperText(__('env.MAIL_FROM_NAME.helper')),
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
