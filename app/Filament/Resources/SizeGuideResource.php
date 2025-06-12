<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SizeGuideResource\Pages;
use App\Models\SizeGuide;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SizeGuideResource extends Resource
{
    protected static ?string $model = SizeGuide::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function getNavigationLabel(): string
    {
        return __('Size Guides');
    }

    public static function getModelLabel(): string
    {
        return __('Size Guide');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Size Guides');
    }

    public static function getLabel(): ?string
    {
        return __('Size Guide');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Size Guides');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Size Information'))
                    ->schema([
                        Select::make('size_id')
                            ->columnSpanFull()
                            ->label(__('Size'))
                            ->relationship('size', 'name')
                            ->required()
                            ->unique(ignoreRecord: true),
                        FileUpload::make('image_path')
                            ->columnSpanFull()
                            ->label(__('Size Guide Image'))
                            ->image()
                            ->required()
                            ->helperText(__('Upload an image showing the size guide for this size')),
                    ])->columns(2),
                Section::make(__('Height Range (cm)'))
                    ->schema([
                        TextInput::make('min_height')
                            ->label(__('Minimum Height'))
                            ->numeric()
                            ->suffix(__('cm'))
                            ->helperText(__('Minimum height for this size')),
                        TextInput::make('max_height')
                            ->label(__('Maximum Height'))
                            ->numeric()
                            ->suffix(__('cm'))
                            ->helperText(__('Maximum height for this size')),
                    ])->columns(2),
                Section::make(__('Weight Range (kg)'))
                    ->schema([
                        TextInput::make('min_weight')
                            ->label(__('Minimum Weight'))
                            ->numeric()
                            ->suffix(__('kg'))
                            ->helperText(__('Minimum weight for this size')),
                        TextInput::make('max_weight')
                            ->label(__('Maximum Weight'))
                            ->numeric()
                            ->suffix(__('kg'))
                            ->helperText(__('Maximum weight for this size')),
                    ])->columns(2),
                Section::make(__('Age Range (years)'))
                    ->schema([
                        TextInput::make('min_age')
                            ->label(__('Minimum Age'))
                            ->numeric()
                            ->suffix(__('years'))
                            ->helperText(__('Minimum age for this size (optional)')),
                        TextInput::make('max_age')
                            ->label(__('Maximum Age'))
                            ->numeric()
                            ->suffix(__('years'))
                            ->helperText(__('Maximum age for this size (optional)')),
                    ])->columns(2),
                Section::make(__('Shoulder Width Range (cm)'))
                    ->schema([
                        TextInput::make('min_shoulder_width')
                            ->label(__('Minimum Shoulder Width'))
                            ->numeric()
                            ->suffix(__('cm'))
                            ->helperText(__('Minimum shoulder width for this size')),
                        TextInput::make('max_shoulder_width')
                            ->label(__('Maximum Shoulder Width'))
                            ->numeric()
                            ->suffix(__('cm'))
                            ->helperText(__('Maximum shoulder width for this size')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->columnSpanFull()
                    ->label(__('Image'))
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('size.name')
                    ->label(__('Size'))
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('height_range')
                    ->label(__('Height Range'))
                    ->getStateUsing(function ($record) {
                        if ($record->min_height && $record->max_height) {
                            return "{$record->min_height} - {$record->max_height} " . __('cm');
                        }
                        return __('Not set');
                    }),
                Tables\Columns\TextColumn::make('weight_range')
                    ->label(__('Weight Range'))
                    ->getStateUsing(function ($record) {
                        if ($record->min_weight && $record->max_weight) {
                            return "{$record->min_weight} - {$record->max_weight} " . __('kg');
                        }
                        return __('Not set');
                    }),
                Tables\Columns\TextColumn::make('shoulder_range')
                    ->label(__('Shoulder Range'))
                    ->getStateUsing(function ($record) {
                        if ($record->min_shoulder_width && $record->max_shoulder_width) {
                            return "{$record->min_shoulder_width} - {$record->max_shoulder_width} " . __('cm');
                        }
                        return __('Not set');
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('Edit')),
                Tables\Actions\DeleteAction::make()->label(__('Delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('Delete Selected')),
                ]),
            ])
            ->defaultSort('size.name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSizeGuides::route('/'),
        ];
    }
}
