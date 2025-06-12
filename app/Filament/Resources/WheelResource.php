<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WheelResource\Pages;
use App\Filament\Resources\WheelResource\RelationManagers;
use App\Models\Wheel;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Route;

class WheelResource extends Resource
{
    use Translatable;

    protected static ?string $model = Wheel::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    public static function getNavigationLabel(): string
    {
        return __('Wheels');
    }

    public static function getModelLabel(): string
    {
        return __('Wheel');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Wheels');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Wheels');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->label(__('Description'))
                        ->nullable()
                        ->maxLength(65535),
                    Forms\Components\TextInput::make('daily_spin_limit')
                        ->label(__('Daily Spin Limit'))
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->default(2),

                    // Timing
                    Forms\Components\TextInput::make('delay_seconds')
                        ->label(__('Delay Seconds'))
                        ->required()
                        ->numeric()
                        ->default(5)
                        ->helperText(__('Delay Seconds Helper')),
                    Forms\Components\TextInput::make('duration_seconds')
                        ->label(__('Display Duration'))
                        ->numeric()
                        ->helperText(__('Display Duration Helper')),
                    Forms\Components\TextInput::make('dont_show_again_days')
                        ->label(__("Hide for (days) when closed with 'Don't show again'"))
                        ->default(30)
                        ->numeric()
                        ->helperText(__('Hide for Days Helper')),
                    Forms\Components\TextInput::make('show_interval_minutes')
                        ->label(__('Interval Between Displays (minutes)'))
                        ->numeric()
                        ->default(60)
                        ->helperText(__('Interval Between Displays Helper')),

                    // Display logic
                    Forms\Components\Select::make('display_rules')
                        ->label(__('Display Rules'))
                        ->options([
                            'all_pages' => __('All Pages'),
                            'specific_pages' => __('Specific Pages'),
                            'page_group' => __('Page Group'),
                            'all_except_specific' => __('All Pages EXCEPT Specific'),
                            'all_except_group' => __('All Pages EXCEPT Group'),
                        ])
                        ->live()
                        ->required()
                        ->helperText(__('Display Rules Helper')),
                    Select::make('specific_pages')
                        ->label(__('Page Rules'))
                        ->multiple()
                        ->searchable()
                        ->visible(fn ($get) => in_array($get('display_rules'), [
                            'specific_pages', 'page_group', 'all_except_specific', 'all_except_group',
                        ]))
                        ->options(fn () => config('frontend-pages'))
                        ->getSearchResultsUsing(function (string $search): array {
                            return collect(config('frontend-pages'))
                                ->filter(fn ($label, $uri) => str_contains($uri, $search) || str_contains($label, $search))
                                ->mapWithKeys(fn ($label, $uri) => [$uri => __($label)])
                                ->toArray();
                        })
                        ->getOptionLabelsUsing(function (array $values): array {
                            return collect($values)
                                ->mapWithKeys(fn ($uri) => [$uri => __(config('frontend-pages')[$uri] ?? $uri)])
                                ->toArray();
                        })
                        ->dehydrateStateUsing(fn ($state) => json_encode($state))
                        ->afterStateHydrated(function ($state, callable $set) {
                            $set('specific_pages', is_string($state) ? json_decode($state, true) : $state);
                        })
                        ->nullable()
                        ->columnSpanFull()
                        ->helperText(__('Page Rules Helper')),
                    Forms\Components\TextInput::make('time_between_spins_minutes')
                        ->label(__('Time Between Spins (Minutes)'))
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->default(30),
                    Forms\Components\Toggle::make('require_phone')
                        ->label(__('Require Phone'))
                        ->default(true),
                    Forms\Components\Toggle::make('is_active')
                        ->label(__('Is Active'))
                        ->default(true),
                ])->columns(1)
            ])->columns(1);
    }

    public static function getPublicPageOptions(): array
    {
        return collect(Route::getRoutes())
            ->filter(fn ($route) =>
                $route->methods() === ['GET'] &&
                $route->uri() !== '/' &&
                !str_starts_with($route->uri(), 'admin') &&
                !str_starts_with($route->uri(), 'client') &&
                !str_contains($route->uri(), '{') // skip dynamic segments like {id}
            )
            ->mapWithKeys(fn ($route) => [$route->uri() => __('/' . $route->uri())])
            ->sort()
            ->all();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(50),
                Tables\Columns\TextColumn::make('daily_spin_limit')
                    ->label(__('Daily Spin Limit'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_between_spins_minutes')
                    ->label(__('Time Between Spins (Minutes)'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('require_phone')
                    ->label(__('Require Phone'))
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Is Active'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('Edit')),
                Tables\Actions\DeleteAction::make()->label(__('Delete')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label(__('Delete Selected')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PrizesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWheels::route('/'),
            'create' => Pages\CreateWheel::route('/create'),
            'edit' => Pages\EditWheel::route('/{record}/edit'),
        ];
    }
}
