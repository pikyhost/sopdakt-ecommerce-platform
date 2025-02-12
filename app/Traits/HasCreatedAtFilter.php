<?php

namespace App\Traits;

use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

trait HasCreatedAtFilter
{
    private static function getCreatedAtFilter(): Filter
    {
        return Filter::make('created_at')
            ->indicator(__('Creation Date Range'))
            ->form([
                DateTimePicker::make('created_from')
                    ->label(__('Creation date from'))
                    ->afterStateUpdated(fn (HasForms $livewire, DateTimePicker $component) => $livewire->validateOnly($component->getStatePath()))
                    ->columnSpan(3),

                DateTimePicker::make('created_until')
                    ->label(__('Creation date until'))
                    ->afterOrEqual('created_from')
                    ->afterStateUpdated(fn (HasForms $livewire, DateTimePicker $component) => $livewire->validateOnly($component->getStatePath()))
                    ->columnSpan(3),
            ])
            ->columns(['sm' => 6, 'lg' => null])
            ->query(fn (Builder $query, array $data) => $query
                ->when($data['created_from'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                ->when($data['created_until'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            )
            ->indicateUsing(function (array $data): ?string {
                $from = $data['created_from'] ?? null;
                $until = $data['created_until'] ?? null;

                if ($from && $until) {
                    return __('Creation date: from :date_from to :date_until', [
                        'date_from' => Carbon::parse($from)->toFormattedDateString(),
                        'date_until' => Carbon::parse($until)->toFormattedDateString()
                    ]);
                }

                if ($from) {
                    return __('Creation date: from :date_from', [
                        'date_from' => Carbon::parse($from)->toFormattedDateString()
                    ]);
                }

                if ($until) {
                    return __('Creation date: until :date_until', [
                        'date_until' => Carbon::parse($until)->toFormattedDateString()
                    ]);
                }

                return null;
            });
    }

}
