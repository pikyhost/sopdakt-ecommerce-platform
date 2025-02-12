<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Livewire\ProfileContactDetails;
use App\Models\CustomFilamentComment;
use App\Policies\CustomFilamentCommentPolicy;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Parallax\FilamentComments\Policies\FilamentCommentPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(CustomFilamentComment::class, CustomFilamentCommentPolicy::class);

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
