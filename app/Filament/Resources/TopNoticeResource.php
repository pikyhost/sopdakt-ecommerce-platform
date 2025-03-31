<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TopNoticeResource\Pages;
use App\Models\TopNotice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class TopNoticeResource extends Resource
{
    protected static ?string $model = TopNotice::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-arrow-up';

    public static function getNavigationLabel(): string
    {
        return __('Top Notice');
    }

    public static function getModelLabel(): string
    {
        return __('Top Notice');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Pages Settings Management');
    }

    public static function getLabel(): ?string
    {
        return __('Top Notice');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\MarkdownEditor::make('content_en')
                        ->label(__('Content (English)'))
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\MarkdownEditor::make('content_ar')
                        ->label(__('Content (Arabic)'))
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('cta_text_en')
                        ->label(__('CTA Text (English)'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('cta_text_ar')
                        ->label(__('CTA Text (Arabic)'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('cta_url')
                        ->label(__('CTA URL'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('cta_text_2_en')
                        ->label(__('CTA Text 2 (English)'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('cta_text_2_ar')
                        ->label(__('CTA Text 2 (Arabic)'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('cta_url_2')
                        ->label(__('CTA URL 2'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('limited_time_text_en')
                        ->label(__('Limited Time Text (English)'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('limited_time_text_ar')
                        ->label(__('Limited Time Text (Arabic)'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('header_message_en')
                        ->label(__('Header Message (English)'))
                        ->nullable(),
                    Forms\Components\TextInput::make('header_message_ar')
                        ->label(__('Header Message (Arabic)'))
                        ->nullable(),
                    Forms\Components\Toggle::make('is_active')
                        ->label(__('Is Active'))
                        ->required(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('content_en')
                    ->html()
                    ->label(__('Content (English)'))
                    ->limit(50),
                Tables\Columns\TextColumn::make('content_ar')
                    ->html()
                    ->label(__('Content (Arabic)'))
                    ->limit(50),
                Tables\Columns\TextColumn::make('cta_text_en')
                    ->label(__('CTA Text (English)')),
                Tables\Columns\TextColumn::make('cta_text_ar')
                    ->label(__('CTA Text (Arabic)')),
                Tables\Columns\TextColumn::make('cta_url')
                    ->label(__('CTA URL')),
                Tables\Columns\TextColumn::make('cta_text_2_en')
                    ->label(__('CTA Text 2 (English)')),
                Tables\Columns\TextColumn::make('cta_text_2_ar')
                    ->label(__('CTA Text 2 (Arabic)')),
                Tables\Columns\TextColumn::make('cta_url_2')
                    ->label(__('CTA URL 2')),
                Tables\Columns\TextColumn::make('limited_time_text_en')
                    ->label(__('Limited Time Text (English)')),
                Tables\Columns\TextColumn::make('limited_time_text_ar')
                    ->label(__('Limited Time Text (Arabic)')),
                Tables\Columns\TextColumn::make('header_message_en')
                    ->label(__('Header Message (English)')),
                Tables\Columns\TextColumn::make('header_message_ar')
                    ->label(__('Header Message (Arabic)')),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Is Active'))
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTopNotices::route('/'),
            'create' => Pages\CreateTopNotice::route('/create'),
            'edit' => Pages\EditTopNotice::route('/{record}/edit'),
        ];
    }
}
