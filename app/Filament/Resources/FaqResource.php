<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    public static function getNavigationLabel(): string
    {
        return __('FAQs');
    }

    public static function getModelLabel(): string
    {
        return __('FAQ');
    }

    public static function getPluralLabel(): ?string
    {
        return __('FAQs');
    }

    public static function getPluralModelLabel(): string
    {
        return __('FAQs');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Select::make('locale')
                        ->label(__('Locale'))
                        ->columnSpanFull()
                        ->default('en')
                        ->required()
                        ->options([
                            'en' => __('English'),
                            'ar' => __('Arabic'),
                        ])->unique(ignoreRecord: true),
                    Repeater::make('items')
                        ->label(__('FAQ Items'))
                        ->schema([
                            TextInput::make('question')
                                ->label(__('Question'))
                                ->required(),
                            Textarea::make('answer')
                                ->label(__('Answer'))
                                ->required(),
                        ])
                        ->columnSpan('full')
                        ->collapsible()
                        ->reorderable(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('locale')
                    ->label(__('Locale'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Last Modified At'))
                    ->since(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('Edit')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
