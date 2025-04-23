<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SizeGuideResource\Pages;
use App\Models\SizeGuide;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SizeGuideResource extends Resource
{
    use Translatable;

    protected static ?string $model = SizeGuide::class;

    protected static ?string $navigationIcon = 'heroicon-o-numbered-list';

    protected static ?string $modelLabel = 'Size Guide';

    protected static ?string $pluralModelLabel = 'Size Guides';

    protected static ?string $navigationLabel = 'Size Guides';

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
                Section::make()->schema([
                    TextInput::make('title')
                        ->label(__('Title'))
                        ->required()
                        ->maxLength(255),
                   Textarea::make('description')
                        ->label(__('Description'))
                        ->required()
                        ->columnSpanFull(),
                    FileUpload::make('image')
                        ->label(__('Image'))
                        ->image()
                        ->required(),
                ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->simpleLightbox()
                    ->label('Image')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(50)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSizeGuides::route('/'),
        ];
    }
}
