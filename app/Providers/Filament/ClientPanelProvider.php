<?php

namespace App\Providers\Filament;

use App\Filament\Client\Pages\Auth\ClientLogin;
use App\Filament\Client\Pages\Auth\ClientRegister;
use App\Livewire\ProfileContactDetails;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;

class ClientPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('client')
            ->path('client')
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Slate,
            ])
            ->login(ClientLogin::class)
            ->registration(ClientRegister::class)
            ->passwordReset()
            ->emailVerification()
            ->discoverResources(in: app_path('Filament/Client/Resources'), for: 'App\\Filament\\Client\\Resources')
            ->discoverPages(in: app_path('Filament/Client/Pages'), for: 'App\\Filament\\Client\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn () => view('footer')
            )
            ->discoverWidgets(in: app_path('Filament/Client/Widgets'), for: 'App\\Filament\\Client\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->spa()
            ->sidebarFullyCollapsibleOnDesktop()
            ->brandName('Ｐｉｋｙ Ｈｏｓｔ')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])->plugins([
                SimpleLightBoxPlugin::make(),
                GlobalSearchModalPlugin::make(),
                BreezyCore::make()
                    ->myProfileComponents([
                        ProfileContactDetails::class,
                    ])
                    ->myProfile(
                        hasAvatars: true,
                    )
                    ->avatarUploadComponent(fn ($fileUpload) => $fileUpload->columnSpan('full')),
            ]);
    }
}
