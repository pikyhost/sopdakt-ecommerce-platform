<?php

namespace App\Livewire;

use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Widgets\Widget;

class Filters extends Widget implements HasForms
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
                Grid::make()
                    ->schema([
                        DatePicker::make('from')
                            ->label(__('Start date'))
                            ->live()
                            ->afterStateUpdated(fn (?string $state) => $this->dispatch('updateFromDate', from: $state)),
                        DatePicker::make('to')
                            ->label(__('End date')) ->live()
                            ->afterStateUpdated(fn (?string $state) => $this->dispatch('updateToDate', to: $state)),
                    ]),
            ]);
    }
}
