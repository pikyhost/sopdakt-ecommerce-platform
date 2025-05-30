<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Analysis;
use App\Filament\Pages\MyGoogleAnalyticsPage;
use App\Filament\Pages\SavedProducts;
use App\Filament\Pages\ServerEnvEditor;
use App\Filament\Pages\Tags;
use App\Filament\Resources\AboutUsResource;
use App\Filament\Resources\AttributeResource;
use App\Filament\Resources\BannerResource;
use App\Filament\Resources\BlockedPhoneNumberResource;
use App\Filament\Resources\BlogCategoryResource;
use App\Filament\Resources\BlogResource;
use App\Filament\Resources\BlogUserLikeResource;
use App\Filament\Resources\BundleResource;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CityResource;
use App\Filament\Resources\ColorResource;
use App\Filament\Resources\ContactMessageResource;
use App\Filament\Resources\ContactResource;
use App\Filament\Resources\ContactSettingResource;
use App\Filament\Resources\CountryGroupResource;
use App\Filament\Resources\CountryResource;
use App\Filament\Resources\CouponResource;
use App\Filament\Resources\CouponUsageResource;
use App\Filament\Resources\CurrencyResource;
use App\Filament\Resources\DiscountResource;
use App\Filament\Resources\FaqResource;
use App\Filament\Resources\GovernorateResource;
use App\Filament\Resources\HomePageSettingResource;
use App\Filament\Resources\InventoryResource;
use App\Filament\Resources\LabelResource;
use App\Filament\Resources\LandingPageOrderResource;
use App\Filament\Resources\LandingPageResource;
use App\Filament\Resources\LandingPageSettingResource;
use App\Filament\Resources\NewsletterSubscriberResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\PaymentMethodResource;
use App\Filament\Resources\PolicyResource;
use App\Filament\Resources\PopupResource;
use App\Filament\Resources\ProductCouponResource;
use App\Filament\Resources\ProductRatingResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\RoleResource;
use App\Filament\Resources\ServiceFeatureResource;
use App\Filament\Resources\SettingResource;
use App\Filament\Resources\ShippingCostResource;
use App\Filament\Resources\ShippingTypeResource;
use App\Filament\Resources\ShippingZoneResource;
use App\Filament\Resources\SizeGuideResource;
use App\Filament\Resources\SizeResource;
use App\Filament\Resources\TopNoticeResource;
use App\Filament\Resources\TransactionResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\WheelPrizeResource;
use App\Filament\Resources\WheelResource;
use App\Filament\Resources\WheelSpinResource;
use App\Models\WheelPrize;
use DragonCode\Support\Facades\Helpers\Str;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\ProductAnalysis;
use App\Livewire\ProfileContactDetails;
use App\Models\Setting;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Jeffgreco13\FilamentBreezy\Pages\MyProfilePage;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;
use Z3d0X\FilamentLogger\Resources\ActivityResource;

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
//            ->emailVerification()
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

            ])
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn() => view('footer')
            )
            ->sidebarCollapsibleOnDesktop()
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->items([
                    ...Dashboard::getNavigationItems(),
                    ...MyProfilePage::getNavigationItems(),
                    ...BlockedPhoneNumberResource::getNavigationItems(),
                    ...ContactMessageResource::getNavigationItems(),
                    ...NewsletterSubscriberResource::getNavigationItems()
                ])->groups($this->getCustomNavigationGroups());
            })
//            ->renderHook('head.end', function () {
//                return view('filament.scripts.navigation-reset');
//            })
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
                '*/admin/products',
                '*/admin/products/*/edit',
                '*/admin/categories/*/edit',
                '*/admin/products/create',
                '*/admin/categories/create',
                '*/admin/analysis',
                '*/admin/blog-categories/create',
                '*/admin/blog-categories/*/edit',

                '*/admin/blogs',
                '*/admin/blogs/*',
            ])
            ->unsavedChangesAlerts()
            ->plugins([
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
            ->databaseNotifications()
            ->renderHook(PanelsRenderHook::SIDEBAR_NAV_START, fn () => view('custom'))
            ->renderHook(PanelsRenderHook::SIDEBAR_NAV_START, fn () => view('navigation-filter'));
    }

    private function getCustomNavigationGroups(): array
    {
        $groups = [
            'analysis' => [
                'label' => __('Analysis'),
                'items' => [
                   Analysis::getNavigationItems(),
                    MyGoogleAnalyticsPage::getNavigationItems(),
                ],
            ],
            'blogs' => [
                'label' => __('Blogs Management'),
                'items' => [
                    BlogCategoryResource::getNavigationItems(),
                    Tags::getNavigationItems(),
                    BlogResource::getNavigationItems(),
                    BlogUserLikeResource::getNavigationItems(),
                ],
            ],
            'products' => [
                'label' => __('Products Management'),
                'items' => [
                    LandingPageResource::getNavigationItems(),
                    ProductResource::getNavigationItems(),
                    LabelResource::getNavigationItems(),
                    BundleResource::getNavigationItems(),
                    AttributeResource::getNavigationItems(),
                    ColorResource::getNavigationItems(),
                    SizeResource::getNavigationItems(),
                    SizeGuideResource::getNavigationItems(),
                    CategoryResource::getNavigationItems()
                ],
            ],
            'inventory' => [
                'label' => __('Stock Management'),
                'items' => [
                     InventoryResource::getNavigationItems(),
                    TransactionResource::getNavigationItems(),
                ],
            ],

            'offers' => [
                'label' => __('Offers'),
                'items' => [
                   DiscountResource::getNavigationItems(),
                    ProductCouponResource::getNavigationItems(),
                    CouponResource::getNavigationItems(),
                    CouponUsageResource::getNavigationItems(),
                    WheelResource::getNavigationItems(),
                    WheelSpinResource::getNavigationItems(),
                    WheelPrizeResource::getNavigationItems(),
                ],
            ],

            'orders-contacts' => [
                'label' => __('landing_page_order.orders_contacts'),
                'items' => [
                    LandingPageOrderResource::getNavigationItems(),
                    OrderResource::getNavigationItems(),
                    ContactResource::getNavigationItems()
                ],
            ],
            'payments' => [
                'label' => __('Payment Management'),
                'items' => [
                   PaymentMethodResource::getNavigationItems()
                ],
            ],
            'shipping' => [
                'label' => __('Shipping Management'),
                'items' => [
                    CityResource::getNavigationItems(),
                    GovernorateResource::getNavigationItems(),
                    ShippingZoneResource::getNavigationItems(),
                    CountryResource::getNavigationItems(),
                    CountryGroupResource::getNavigationItems(),
                    ShippingTypeResource::getNavigationItems(),
                    ShippingCostResource::getNavigationItems(),
                ],
            ],
            'user-experience' => [
                'label' => __('user_experience'),
                'items' => [
                    SavedProducts::getNavigationItems(),
                   ProductRatingResource::getNavigationItems()
                ],
            ],
            'settings' => [
                'label' => __('Settings Management'),
                'items' => [
                    ServerEnvEditor::getNavigationItems(),
                    ContactSettingResource::getNavigationItems(),
                    ActivityResource::getNavigationItems(),
                    LandingPageSettingResource::getNavigationItems(),
                    SettingResource::getNavigationItems(),
                    CurrencyResource::getNavigationItems(),
                    UserResource::getNavigationItems(),
                    RoleResource::getNavigationItems(),

                ],
            ],
            'pages-settings' => [
                'label' => __('Pages Settings Management'),
                'items' => [
                    AboutUsResource::getNavigationItems(),
                    FaqResource::getNavigationItems(),
                    TopNoticeResource::getNavigationItems(),
                    BannerResource::getNavigationItems(),
                    HomePageSettingResource::getNavigationItems(),
                    ServiceFeatureResource::getNavigationItems(),
                    PopupResource::getNavigationItems(),
                    PolicyResource::getNavigationItems(),
                ],
            ],
        ];

        $navigationGroups = [];

        foreach ($groups as $group) {
            $items = collect($group['items'])->flatten()->values();

            $hasActiveItem = $items->contains(function (NavigationItem $item) {
                $itemUrl = $item->getUrl();
                $currentUrl = URL::current();

                // This allows us to match if current page is under the item's base path
                return $itemUrl && Str::startsWith($currentUrl, $itemUrl);
            });

            $groupBuilder = NavigationGroup::make($group['label'])
                ->items($items->all());

            if (! $hasActiveItem) {
                $groupBuilder->collapsed();
            }

            $navigationGroups[] = $groupBuilder;
        }

        return $navigationGroups;
    }
}
