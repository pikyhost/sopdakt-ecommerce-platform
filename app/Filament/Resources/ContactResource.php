<?php

namespace App\Filament\Resources;


use App\Models\Contact;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\ContactResource\Pages;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;
    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Orders';
    protected static ?string $navigationLabel = 'Contacts';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->label('Name')->sortable(),
                TextColumn::make('email')->label('Email')->sortable(),
                TextColumn::make('phone')->label('Phone')->sortable(),
                TextColumn::make('message')->label('Message')->limit(100),
                TextColumn::make('created_at')->label('Date')->dateTime('M d, Y H:i A')->sortable(),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
        ];
    }
}
