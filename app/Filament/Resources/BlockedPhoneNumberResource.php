<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlockedPhoneNumberResource\Pages;
use App\Models\BlockedPhoneNumber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class BlockedPhoneNumberResource extends Resource
{
    use Translatable;

    protected static ?string $model = BlockedPhoneNumber::class;

    protected static ?string $navigationIcon = 'heroicon-o-no-symbol';

    public static function getNavigationLabel(): string
    {
        return __('blocked_phones.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('blocked_phones.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('blocked_phones.plural');
    }

    public static function getPluralLabel(): ?string
    {
        return __('blocked_phones.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(1)->schema([
                    PhoneInput::make('phone_number')
                        ->enableIpLookup(true) // Enable IP-based country detection
                        ->initialCountry(fn () => geoip(request()->ip())['country_code2'] ?? 'US')
                        ->label(__('blocked_phones.phone_number'))
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\Textarea::make('reason')
                        ->label(__('Reason')),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phone_number')
                    ->label(__('blocked_phones.phone_number'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label(__('blocked_phones.note'))
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('blocked_phones.created_at'))
                    ->dateTime('Y-m-d H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBlockedPhoneNumbers::route('/'),
        ];
    }
}
