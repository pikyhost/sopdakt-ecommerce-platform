<?php

namespace App\Providers\Filament;

use App\Livewire\ProfileContactDetails;
use App\Models\Setting;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use DragonCode\Support\Facades\Helpers\Str;
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
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = Setting::getAllSettings();

        // Get the correct favicon path based on locale
        $faviconPath = $settings["favicon_en"] ?? null;

        $favicon = $faviconPath ? Storage::url($faviconPath) : asset('images/clients/client1.png');

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->emailVerification()
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Slate,
            ])
            ->brandLogo(fn() => view('filament.app.logo'))
            ->favicon($favicon)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn() => view('footer')
            )
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups($this->getNavigationGroups())
            ->renderHook('head.end', function () {
                return view('filament.scripts.navigation-reset');
            })
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->visible(fn() => Filament::auth()->check())
                    ->url(url('/admin/my-profile')) // Adjusted route helper here
                    ->icon('heroicon-m-user-circle'),
                'logout' => MenuItem::make(),
            ])
//            ->brandName('Ｐｉｋｙ Ｈｏｓｔ')
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
            ->sidebarFullyCollapsibleOnDesktop()
            ->authMiddleware([
                Authenticate::class,
            ])
            ->spa()
            ->spaUrlExceptions([
                '*/admin/products/*/edit',
                '*/admin/categories/*/edit',
                '*/admin/products/create',
                '*/admin/categories/create',
            ])
            ->unsavedChangesAlerts()
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                SimpleLightBoxPlugin::make(),
                SpatieLaravelTranslatablePlugin::make()->defaultLocales(['en', 'ar']),
                GlobalSearchModalPlugin::make(),
                BreezyCore::make()
                    ->myProfileComponents([
                        ProfileContactDetails::class,
                    ])
                    ->myProfile(
                        hasAvatars: true,
                    )
                    ->avatarUploadComponent(fn($fileUpload) => $fileUpload->columnSpan('full')),
            ]);
    }

    private function getNavigationGroups(): array
    {
        $groups = [
            'Products Management',
            'Inventory Management',
            'Orders',
            'Shipping Management',
            'user_experience',
            'Settings Management',
        ];

        // Create NavigationGroup instances
        $navigationGroups = array_map(fn($group) => NavigationGroup::make(__($group)), $groups);

        // Find the active group
        $activeGroup = null;
        foreach ($navigationGroups as $navigationGroup) {
            if ($navigationGroup->isActive()) {
                $activeGroup = $navigationGroup->getLabel();
                break;
            }
        }

        // Ensure all groups are collapsed by default, except the active one
        return array_map(fn($navigationGroup) => $navigationGroup->collapsed($navigationGroup->getLabel() !== $activeGroup),
            $navigationGroups
        );
    }
}
