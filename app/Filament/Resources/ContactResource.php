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

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('landing_page_order.contacts.id'))->sortable(),
                TextColumn::make('name')->label(__('landing_page_order.contacts.name'))->sortable(),
                TextColumn::make('email')->label(__('landing_page_order.contacts.email'))->sortable(),
                TextColumn::make('phone')->label(__('landing_page_order.contacts.phone'))->sortable(),
                TextColumn::make('message')->label(__('landing_page_order.contacts.message'))->limit(100),
                TextColumn::make('created_at')->label(__('landing_page_order.contacts.created_at'))->dateTime('M d, Y H:i A')->sortable(),
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
