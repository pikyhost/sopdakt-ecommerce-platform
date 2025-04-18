<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PopupResource\Pages;
use App\Helpers\PageSuggestion;
use App\Models\Popup;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PopupResource extends Resource
{
    use Translatable;

    protected static ?string $model = Popup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('Popups');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    public static function getModelLabel(): string
    {
        return __('Popup');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Popups');
    }

    public static function getLabel(): ?string
    {
        return __('Popup');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Popups');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('Title'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('description')
                        ->label(__('Description'))
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('image_path')
                        ->label(__('Image'))
                        ->image(),

                    Forms\Components\TextInput::make('cta_text')
                        ->label(__('CTA Text'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('cta_link')
                        ->label(__('CTA Link'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('delay_seconds')
                        ->label(__('Delay Seconds'))
                        ->required()
                        ->numeric()
                        ->default(5),

                    Forms\Components\TextInput::make('duration_seconds')
                        ->label(__('Display Duration'))
                        ->numeric()
                        ->helperText(__('How long the popup remains visible before hiding automatically (0 = until manually closed).')),

                    Forms\Components\TextInput::make('dont_show_again_days')
                        ->label(__('Hide for (days) when closed with "Don\'t show again"'))
                        ->default(30)
                        ->numeric(),

                    Forms\Components\Select::make('display_rules')
                        ->label('Display Rules')
                        ->options([
                            'all_pages' => 'All Pages',
                            'specific_pages' => 'Specific Pages',
                            'page_group' => 'Page Group',
                            'all_except_specific' => 'All Pages EXCEPT Specific',
                            'all_except_group' => 'All Pages EXCEPT Group',
                        ])
                        ->live()
                        ->required(),

                    Forms\Components\Textarea::make('specific_pages')
                        ->label('Page Rules')
                        ->helperText('أدخل كل رابط في سطر. مثل: contact أو blog/news')
                        ->visible(fn ($get) => in_array($get('display_rules'), [
                            'specific_pages', 'page_group', 'all_except_specific', 'all_except_group'
                        ])),

                    Forms\Components\TextInput::make('popup_order')
                        ->label('Popup Order')
                        ->numeric()
                        ->default(0)
                        ->helperText('كلما كان الرقم أصغر، يظهر البوب أب أولاً'),

                    Forms\Components\TextInput::make('show_interval_minutes')
                        ->label('Interval Between Displays (minutes)')
                        ->numeric()
                        ->default(60)
                        ->helperText('كم دقيقة يجب أن تمر قبل عرض بوب أب آخر'),


                    Forms\Components\Checkbox::make('email_needed')
                        ->label(__('Email needed?')),

                    Forms\Components\Toggle::make('is_active')
                        ->columnSpanFull()
                        ->label(__('Is Active'))
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(),

                Tables\Columns\ImageColumn::make('image_path')
                    ->label(__('Image')),

                Tables\Columns\TextColumn::make('cta_text')
                    ->label(__('CTA Text'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('cta_link')
                    ->label(__('CTA Link'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('delay_seconds')
                    ->label(__('Delay Seconds'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Is Active'))
                    ->boolean(),


                Tables\Columns\IconColumn::make('email_needed')
                    ->label(__('Email needed?'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('display_rules')
                    ->label(__('Display Rules')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('Edit')),
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
            'index' => Pages\ListPopups::route('/'),
            'create' => Pages\CreatePopup::route('/create'),
            'edit' => Pages\EditPopup::route('/{record}/edit'),
        ];
    }
}
