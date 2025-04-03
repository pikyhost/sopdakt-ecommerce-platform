<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextArea;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;

class BannerResource extends Resource
{
    use Translatable;

    protected static ?string $model = Banner::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-top-right-on-square';
    protected static ?string $navigationLabel = 'Banners';
    protected static ?string $pluralLabel = 'Banners';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label(__('Title'))
                    ->required(),

                TextArea::make('subtitle')
                    ->label(__('Subtitle'))
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
                    ->label(__('Image'))
                    ->image()
                    ->required(),

                Select::make('type')
                    ->label(__('Type'))
                    ->options([
                        'product' => __('Product'),
                        'category' => __('Category'),
                    ])
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label(__('Image')),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->translateLabel(),

                TextColumn::make('type')
                    ->label(__('Type'))
                    ->translateLabel()
                    ->sortable(),
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
