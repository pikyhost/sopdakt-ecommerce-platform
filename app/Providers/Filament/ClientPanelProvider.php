<?php

namespace App\Providers\Filament;

use App\Filament\Client\Pages\Auth\ClientLogin;
use App\Filament\Client\Pages\Auth\ClientRegister;
use App\Filament\Widgets\HomePageRedirect;
use App\Livewire\ProfileContactDetails;
use App\Models\Setting;
use App\Rules\CustomPassword;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;

class ClientPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = Setting::getAllSettings();

        $faviconPath = $settings["favicon"] ?? null;

        $favicon = $faviconPath ? Storage::url($faviconPath) : asset('images/clients/client1.png');

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
            ->favicon($favicon)
            ->discoverResources(in: app_path('Filament/Client/Resources'), for: 'App\\Filament\\Client\\Resources')
            ->discoverPages(in: app_path('Filament/Client/Pages'), for: 'App\\Filament\\Client\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->brandLogo(fn () => view('filament.app.logo'))
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn () => view('footer')
            )
            ->discoverWidgets(in: app_path('Filament/Client/Widgets'), for: 'App\\Filament\\Client\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->spa()
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->visible(fn() => Filament::auth()->check())
                    ->url(url('/client/my-profile')) // Adjusted route helper here
                    ->icon('heroicon-m-user-circle'),
                'logout' => MenuItem::make(),
            ])
            ->sidebarFullyCollapsibleOnDesktop()
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
            ->navigationGroups($this->getNavigationGroups())
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->authMiddleware([
                Authenticate::class,
            ])->plugins([
                SimpleLightBoxPlugin::make(),
                GlobalSearchModalPlugin::make(),
                BreezyCore::make()
                    ->passwordUpdateRules(
                        rules: ['min:8', new CustomPassword()] // Minimum 8 characters & custom rule
                    )
                    ->myProfileComponents([
                        ProfileContactDetails::class,
                    ])
                    ->myProfile(
                        hasAvatars: true,
                        shouldRegisterNavigation: true
                    )
                    ->avatarUploadComponent(fn ($fileUpload) => $fileUpload->columnSpan('full')),
            ]);
    }

    private function getNavigationGroups(): array
    {
        $groups = [
            __('My orders'),
            __('policy.pages_group')
        ];

        $navigationGroups = array_map(fn($group) => NavigationGroup::make(__($group)), $groups);

        $activeGroup = null;
        foreach ($navigationGroups as $navigationGroup) {
            if ($navigationGroup->isActive()) {
                $activeGroup = $navigationGroup->getLabel();
                break;
            }
        }

        return array_map(fn($navigationGroup) => $navigationGroup->collapsed($navigationGroup->getLabel() !== $activeGroup),
            $navigationGroups
        );
    }
}
