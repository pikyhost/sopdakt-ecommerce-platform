<?php

namespace App\Filament\Resources;

use App\Models\ServiceFeature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceFeatureResource extends Resource
{
    use Translatable;

    protected static ?string $model = ServiceFeature::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->live(onBlur: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('subtitle')
                        ->maxLength(255),

                    Forms\Components\FileUpload::make('icon')
                        ->hint('Use Heroicons or any other icon library SVG'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subtitle')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\ImageColumn::make('icon')->circular(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => \App\Filament\Resources\ServiceFeatureResource\Pages\ListServiceFeatures::route('/'),
            'create' => \App\Filament\Resources\ServiceFeatureResource\Pages\CreateServiceFeature::route('/create'),
            'edit' => \App\Filament\Resources\ServiceFeatureResource\Pages\EditServiceFeature::route('/{record}/edit'),
        ];
    }
}
