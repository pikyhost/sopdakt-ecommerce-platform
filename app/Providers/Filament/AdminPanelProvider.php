<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Livewire\ProfileContactDetails;
use App\Models\Setting;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use GeoSot\FilamentEnvEditor\FilamentEnvEditorPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = Setting::getAllSettings();

        $faviconPath = $settings["favicon"] ?? null;

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
               Dashboard::class,
            ])
            ->resources([
                config('filament-logger.activity_resource')
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
            ->viteTheme('resources/css/filament/admin/theme.css')
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
                FilamentEnvEditorPlugin::make()
                    ->hideKeys(
                        'APP_ENV',
                        'APP_MAINTENANCE_STORE',
                        'APP_KEY',
                        'APP_DEBUG',
                        'APP_TIMEZONE',
                        'APP_URL',
                        'APP_LOCALE',
                        'APP_FALLBACK_LOCALE',
                        'APP_FAKER_LOCALE',
                        'APP_MAINTENANCE_DRIVER',
                        'PHP_CLI_SERVER_WORKERS',
                        'BCRYPT_ROUNDS',
                        'LOG_CHANNEL',
                        'LOG_STACK',
                        'LOG_DEPRECATIONS_CHANNEL',
                        'LOG_LEVEL',

                        // Database Settings
                        'DB_CONNECTION',
                        'DB_HOST',
                        'DB_PORT',
                        'DB_DATABASE',
                        'DB_USERNAME',
                        'DB_PASSWORD',

                        // Session & Caching
                        'SESSION_DRIVER',
                        'SESSION_LIFETIME',
                        'SESSION_ENCRYPT',
                        'SESSION_PATH',
                        'SESSION_DOMAIN',
                        'CACHE_STORE',
                        'CACHE_PREFIX',
                        'MEMCACHED_HOST',
                        'REDIS_CLIENT',
                        'REDIS_HOST',
                        'REDIS_PASSWORD',
                        'REDIS_PORT',

                        // Broadcast & Queue
                        'BROADCAST_CONNECTION',
                        'FILESYSTEM_DISK',
                        'QUEUE_CONNECTION',

                        // AWS Storage
                        'AWS_ACCESS_KEY_ID',
                        'AWS_SECRET_ACCESS_KEY',
                        'AWS_DEFAULT_REGION',
                        'AWS_BUCKET',
                        'AWS_USE_PATH_STYLE_ENDPOINT',

                        // Vite & GeoIP
                        'VITE_APP_NAME',
                        'GEOIP_IPGEOLOCATION_KEY',
                        'GEOIP_SERVICE',

                        // JT Express API
                        'JT_EXPRESS_BASE_URL',
                        'JT_EXPRESS_API_ACCOUNT',
                        'JT_EXPRESS_PRIVATE_KEY',
                        'JT_EXPRESS_CUSTOMER_CODE',
                        'JT_EXPRESS_PASSWORD'
                    )
                    ->navigationGroup(__('Settings Management'))
                    ->navigationLabel(__('My Env'))
                    ->navigationIcon('heroicon-o-wrench-screwdriver')
                    ->navigationSort(1)
                    ->slug('env-editor'),
                \BezhanSalleh\FilamentGoogleAnalytics\FilamentGoogleAnalyticsPlugin::make(),
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
                        shouldRegisterNavigation: true
                    )
                    ->avatarUploadComponent(fn ($fileUpload) => $fileUpload->columnSpan('full')),
            ])
            ->renderHook(PanelsRenderHook::SIDEBAR_NAV_START, fn () => view('navigation-filter'));
    }

    private function getNavigationGroups(): array
    {
        $groups = [
            __('Analysis'),
            __('Products Management'),
            __('Inventory Management'),
            __('Orders'),
            __('Orders & Contacts'),
            __('Payment Management'),
            __('Shipping Management'),
            __('user_experience'),
            __('Settings Management'),
            __('Pages Settings Management')
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
