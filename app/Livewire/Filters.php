<?php

namespace App\Livewire;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
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
                Section::make()
                    ->description(__('filter1_desc'))
                    ->schema([
                        DatePicker::make('from')
                            ->label(__('Start date'))
                            ->live()
                            ->afterStateUpdated(fn (?string $state) =>
                            filled($state) ? $this->dispatch('updateFromDate1', from: $state) : null
                            ),

                        DatePicker::make('to')
                            ->label(__('End date'))
                            ->live()
                            ->afterStateUpdated(fn (?string $state) =>
                            filled($state) ? $this->dispatch('updateToDate1', to: $state) : null
                            ),
                    ])->columns(2),

                Section::make()
                    ->description(__('filter2_desc'))
                    ->schema([
                        DatePicker::make('from2')
                            ->label(__('Start date for Sales Chart 2'))
                            ->live()
                            ->afterStateUpdated(fn (?string $state) =>
                            filled($state) ? $this->dispatch('updateFromDate2', from: $state) : null
                            ),

                        DatePicker::make('to2')
                            ->label(__('End date for Sales Chart 2'))
                            ->live()
                            ->afterStateUpdated(fn (?string $state) =>
                            filled($state) ? $this->dispatch('updateToDate2', to: $state) : null
                            ),
                    ])->columns(2),
            ])->columns(2);
    }

}
