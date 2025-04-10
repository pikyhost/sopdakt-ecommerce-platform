<?php

namespace App\Livewire;

use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Widgets\Widget;

class DashboardFilter extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'livewire.filters';

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                DatePicker::make('fromDashboard')
                    ->label(__('Start date'))
                    ->live()
                    ->afterStateUpdated(fn (?string $state) =>
                    filled($state) ? $this->dispatch('updateFromDateDashboard', $state) : null
                    ),

                DatePicker::make('toDashboard')
                    ->label(__('End date'))
                    ->live()
                    ->afterStateUpdated(fn (?string $state) =>
                    filled($state) ? $this->dispatch('updateToDateDashboard', $state) : null
                    ),
            ])->columns(2);
    }

}
