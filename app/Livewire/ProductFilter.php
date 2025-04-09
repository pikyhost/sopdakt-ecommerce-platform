<?php

namespace App\Livewire;

use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Widgets\Widget;

class ProductFilter extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'livewire.filters';
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $defaultFrom = now()->subMonth()->format('Y-m-d');
        $defaultTo = now()->format('Y-m-d');

        $this->form->fill([
            'fromProduct' => $defaultFrom,
            'toProduct' => $defaultTo,
        ]);

        // Dispatch default values on initial mount
        $this->dispatch('updateFromDateProduct', from: $defaultFrom);
        $this->dispatch('updateToDateProduct', to: $defaultTo);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                DatePicker::make('fromProduct')
                    ->label(__('Start date'))
                    ->native(false)
                    ->closeOnDateSelection()
                    ->live()
                    ->afterStateUpdated(fn (?string $state) =>
                    $this->dispatch('updateFromDateProduct', from: $state)
                    ),
                DatePicker::make('toProduct')
                    ->label(__('End date'))
                    ->native(false)
                    ->closeOnDateSelection()
                    ->live()
                    ->afterStateUpdated(fn (?string $state) =>
                    $this->dispatch('updateToDateProduct', to: $state)
                    ),
            ])
            ->columns(2);
    }
}
