<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;

class BannerResource extends Resource
{
    use Translatable;

    protected static ?string $model = Banner::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-2';
    protected static ?string $navigationLabel = 'Banners';
    protected static ?string $pluralLabel = 'Banners';

    public static function getNavigationGroup(): ?string
    {
        return __('Pages Settings Management');
    }

    public static function getModelLabel(): string
    {
        return __('Menu Banners');
    }

    public static function getNavigationLabel(): string
    {
        return __('Menu Banners');
    }

    public static function getLabel(): string
    {
        return __('Menu Banners');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Select::make('type')
                        ->columnSpanFull()
                        ->disabled()
                        ->label(__('Type'))
                        ->options([
                            'product' => __('Product'),
                            'category' => __('Category'),
                        ])
                        ->required(),

                    TextInput::make('subtitle')
                        ->label(__('Title'))
                        ->nullable(),

                    TextInput::make('discount')
                        ->label(__('Discount'))
                        ->nullable(),

                    TextInput::make('button_text')
                        ->label(__('Button Text'))
                        ->nullable(),

                    TextInput::make('button_url')
                        ->label(__('Button URL'))
                        ->nullable(),

                    FileUpload::make('image')
                        ->imageEditor()
                        ->columnSpanFull()
                        ->label(__('Image'))
                        ->image()
                        ->required(),
                ])->columns(2)
            ])->columns(2);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label(__('Image')),

                TextColumn::make('subtitle')
                    ->label(__('Title')),

                TextColumn::make('discount')
                    ->label(__('Discount')),

                TextColumn::make('type')
                    ->badge()
                    ->color('primary')
                    ->label(__('Type')),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
