<?php

namespace App\Traits;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

trait HasTimestampsDetailsSection
{
    private static function getTimestampsDetailsSection(): Section
    {
        return Section::make('Timestamps Details')
            ->visible(fn ($record) => auth()->user()->can('view_timestamps_details_book'))
            ->schema([
                TextEntry::make('created_at')
                    ->inlineLabel()
                    ->label('Date Added to Library')
                    ->dateTime('D, M j, Y \a\t g:i A')
                    ->visible(fn ($record) => auth()->user()->can('view_timestamps_details_book')),

                TextEntry::make('updated_at')
                    ->inlineLabel()
                    ->label('Last Modified At')
                    ->dateTime('D, M j, Y \a\t g:i A')
                    ->visible(fn ($record) => auth()->user()->can('view_timestamps_details_book')),

                TextEntry::make('deleted_at')
                    ->inlineLabel()
                    ->label('Deleted At')
                    ->badge()
                    ->color('danger')
                    ->dateTime('D, M j, Y \a\t g:i A')
                    ->visible(fn ($record) => $record->trashed() && auth()->user()->can('view_timestamps_details_book')),
            ]);
    }
}
