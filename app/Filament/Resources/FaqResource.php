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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Select::make('locale')
                        ->columnSpanFull()
                        ->default('en')
                        ->required()
                        ->options([
                            'en' => 'English',
                            'ar' => 'Arabic (العربية)',
                        ]),

                    Repeater::make('items')
                        ->label('FAQ Items')
                        ->schema([
                            TextInput::make('question')
                                ->label('Question')
                                ->required(),
                            Textarea::make('answer')
                                ->label('Answer')
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
                Tables\Columns\TextColumn::make('locale')->searchable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Last Modified At'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
