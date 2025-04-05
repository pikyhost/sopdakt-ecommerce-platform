<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\HomePageRedirect;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
                            ->native()
                            ->label(__('Start date')),
                        DatePicker::make('endDate')
                            ->native()
                            ->label(__('End date'))
                            ->minDate(fn (Get $get) => $get('startDate') ?: now()),
                    ])
                    ->columns(2),
            ]);
    }
}
