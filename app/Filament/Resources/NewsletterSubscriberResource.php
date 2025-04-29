<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterSubscriberResource\Pages;
use App\Models\NewsletterSubscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    public static function getNavigationLabel(): string
    {
        return __('Newsletter Subscribers');
    }

    public static function getModelLabel(): string
    {
        return __('Newsletter Subscriber');
    }

    public static function getLabel(): ?string
    {
        return __('Newsletter Subscriber');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Newsletter Subscribers');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Newsletter Subscribers');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Pages Settings Management');
    }

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->columnSpanFull()
                    ->label(__('Email'))
                    ->email()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label(__('IP Address')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('Edit')),
                Tables\Actions\DeleteAction::make()->label(__('Delete')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label(__('Delete Selected')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNewsletterSubscribers::route('/'),
        ];
    }
}
