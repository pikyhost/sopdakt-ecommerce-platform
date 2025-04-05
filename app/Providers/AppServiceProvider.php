<?php

namespace App\Providers;

use App\Http\Responses\LogoutResponse;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Navigation\NavigationGroup;
use Livewire\Livewire;
use App\Enums\UserRole;
use Filament\Tables\Table;
use App\Services\JtExpressService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Livewire\ProfileContactDetails;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\ServiceProvider;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\FiltersLayout;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    // asaber91@gmail.com
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);

        $this->app->singleton(JtExpressService::class, function ($app) {
            return new JtExpressService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ProfileContactDetails::setSort(10);

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->visible(outsidePanels: true)
                ->locales(['en','ar']);
        });

        Gate::before(function ($user, $ability) {
            if ($user->hasRole(UserRole::SuperAdmin->value)) {
                return true;
            }
        });

        $this->configureTextColumn();
        $this->configureTextInput();
        $this->configureTable();

        // Configure Livewire update route
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post(LaravelLocalization::setLocale() . '/livewire/update', $handle)
                ->middleware(['web', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']);
        });

        // Configure Livewire JavaScript route
        Livewire::setScriptRoute(function ($handle) {
            return Route::get(LaravelLocalization::setLocale() . '/livewire/livewire.js', $handle)
                ->middleware(['web', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']);
        });
    }


    protected function configureTextColumn(): void
    {
        TextColumn::configureUsing(function (TextColumn $column) {
            $column->limit(23)
                ->tooltip(fn (TextColumn $column): ?string => $this->getTooltip($column))
                ->toggleable(true, fn () => $column->isToggledHiddenByDefault ?? false);
        });
    }

    protected function configureTextInput(): void
    {
        TextInput::configureUsing(fn (TextInput $textInput) => $textInput->maxLength(255));
    }

    protected function configureTable(): void
    {
        Table::configureUsing(function (Table $table): void {
            $table->filtersLayout(FiltersLayout::AboveContent)
                ->emptyStateHeading(__('No Records Found'))
                ->emptyStateDescription(__('There are no items to display.'))
                ->defaultSort('id', 'desc')
                ->poll(null)
                ->paginationPageOptions([10, 25, 50]);
        });
    }

    protected function getTooltip(TextColumn $column): ?string
    {
        $state = $column->getState();

        return is_string($state) && strlen($state) > $column->getCharacterLimit() ? $state : null;
    }

}
