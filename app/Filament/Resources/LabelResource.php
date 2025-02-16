<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LabelResource\Pages;
use App\Models\Label;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LabelResource extends Resource
{
    use Translatable;

    protected static ?string $model = Label::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.products_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('labels.label');
    }

    public static function getModelLabel(): string
    {
        return __('labels.label');
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('labels.plural_label');
    }

    public static function getLabel(): ?string
    {
        return __('labels.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('labels.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('fields.text_title'))
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('color')
                        ->label(__('fields.text_color')),
                    Forms\Components\ColorPicker::make('color_code')
                        ->label(__('fields.text_color_code')),
                    Forms\Components\TextInput::make('background_color')
                        ->label(__('fields.background_color')),
                    Forms\Components\ColorPicker::make('background_color_code')
                        ->label(__('fields.background_color_code')),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('fields.text_title'))
                    ->columnSpanFull(),
                Tables\Columns\TextColumn::make('color')
                    ->label(__('fields.text_color'))
                    ->searchable(),
                Tables\Columns\ColorColumn::make('color_code')
                    ->label(__('fields.text_color_code'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('background_color')
                    ->label(__('fields.background_color'))
                    ->searchable(),
                Tables\Columns\ColorColumn::make('background_color_code')
                    ->label(__('fields.background_color_code'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('actions.bulk_delete')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLabels::route('/'),
        ];
    }
}
