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

    protected static ?string $modelLabel = 'Service Feature';

    protected static ?string $navigationGroup = 'Services';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Label')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('subtitle')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('icon')
                                    ->maxLength(255)
                                    ->hint('Use Heroicons or any other icon library class names'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Translations')
                            ->schema([
                                Forms\Components\Repeater::make('translations')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Title Translation')
                                            ->required(),

                                        Forms\Components\TextInput::make('subtitle')
                                            ->label('Subtitle Translation'),

                                        Forms\Components\Select::make('locale')
                                            ->label('Language')
                                            ->required()
                                            ->options(config('app.available_locales')),
                                    ])
                                    ->columns(1),
                            ]),
                    ]),
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

                Tables\Columns\IconColumn::make('icon')
                    ->icon(fn (string $state): string => $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
