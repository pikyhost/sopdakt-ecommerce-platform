<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Mail\OfferEmail;
use App\Models\Contact;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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


                TextColumn::make('address')->label('Address'),

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

                    Tables\Actions\BulkAction::make('sendOfferOrMessage')
                        ->label(__('Send Offer or Update'))
                        ->icon('heroicon-o-paper-airplane')
                        ->form([
                            Select::make('type')
                                ->label(__('Choose what to send'))
                                ->options([
                                    'discount' => __('Promotional Discount'),
                                    'product'  => __('Product Highlight'),
                                    'article'  => __('Blog Article'),
                                    'custom'   => __('Custom Message'),
                                ])
                                ->required()
                                ->live(),

                            Select::make('discount_id')
                                ->label(__('Select a Discount'))
                                ->options(fn () => \App\Models\Discount::active()->pluck('name', 'id'))
                                ->visible(fn (Get $get) => $get('type') === 'discount')
                                ->required(fn (Get $get) => $get('type') === 'discount'),

                            Select::make('product_id')
                                ->label(__('Select a Product'))
                                ->options(fn () => \App\Models\Product::pluck('name', 'id'))
                                ->visible(fn (Get $get) => $get('type') === 'product')
                                ->required(fn (Get $get) => $get('type') === 'product'),

                            Select::make('blog_id')
                                ->label(__('Select a Blog Article'))
                                ->options(fn () => \App\Models\Blog::latest()->pluck('title', 'id'))
                                ->visible(fn (Get $get) => $get('type') === 'article')
                                ->required(fn (Get $get) => $get('type') === 'article'),

                            RichEditor::make('custom_message')
                                ->label(__('Write your message'))
                                ->visible(fn (Get $get) => $get('type') === 'custom')
                                ->required(fn (Get $get) => $get('type') === 'custom')
                                ->fileAttachmentsDirectory('emails')
                        ])
                        ->action(fn (Collection $records, array $data) => static::sendOffer($records, $data))
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle(__('Your message was successfully sent!'))
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageContacts::route('/'),
        ];
    }

    public static function sendOffer(Collection $users, array $data): void
    {
        try {
            foreach ($users as $user) {
                Mail::to($user->email)->sendNow(new OfferEmail($user, $data));
            }
        } catch (\Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());
        }

        Notification::make()
            ->title(__('Your message was successfully sent!'))
            ->success()
            ->send();
    }
}
