<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->label(__('Start date'))
                            ->default(now()->subMonth()->startOfMonth()),
                        DatePicker::make('endDate')
                            ->label(__('End date'))
                            ->default(now()->endOfMonth()),
                    ])
                    ->columns(2),
            ]);
    }
}
