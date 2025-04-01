<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('landing_page_order.orders_contacts');
    }

    public static function getNavigationLabel(): string
    {
        return __('landing_page_order.contacts.contacts');
    }

    public static function getModelLabel(): string
    {
        return __('landing_page_order.contacts.contact');
    }

    public static function getPluralLabel(): ?string
    {
        return __('landing_page_order.contacts.contacts');
    }

    public static function getLabel(): ?string
    {
        return __('landing_page_order.contacts.contacts');
    }

    public static function getPluralModelLabel(): string
    {
        return __('landing_page_order.contacts.contacts');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('session_id')
                    ->label(__('Session ID'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable(),

                TextColumn::make('phone')
                    ->iconColor('primary')
                    ->icon('heroicon-o-phone')
                    ->label(__('Phone'))
                    ->placeholder(__('No phone number saved'))
                    ->searchable(),

                TextColumn::make('second_phone')
                    ->iconColor('primary')
                    ->icon('heroicon-o-phone')
                    ->label(__('Second Phone'))
                    ->placeholder(__('No phone number saved'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('landing_page_id')
                    ->label(__('Landing Page ID'))
                    ->placeholder(__('Not Available'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                DateFilter::make('created_at')
                    ->label(__('Created At')),
            ], Tables\Enums\FiltersLayout::Modal)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Delete Selected')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageContacts::route('/'),
        ];
    }
}
