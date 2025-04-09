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
        $this->form->fill([
            'fromProduct' => now()->subMonth()->format('Y-m-d'),
            'toProduct' => now()->format('Y-m-d'),
        ]);
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
                    filled($state) ? $this->dispatch('updateFromDateProduct', from: $state) : null
                    ),
                DatePicker::make('toProduct')
                    ->label(__('End date'))
                    ->native(false)
                    ->closeOnDateSelection()
                    ->live()
                    ->afterStateUpdated(fn (?string $state) =>
                    filled($state) ? $this->dispatch('updateToDateProduct', to: $state) : null
                    ),
            ])
            ->columns(2);
    }
}
