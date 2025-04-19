<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WheelResource\Pages;
use App\Models\Wheel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WheelResource extends Resource
{
    protected static ?string $model = Wheel::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?string $navigationGroup = 'wheel_of_fortune';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Basic Information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->columnSpanFull()
                            ->required()
                            ->maxLength(255)
                            ->label(__('Name')),
                        Forms\Components\Checkbox::make('is_active')
                            ->columnSpanFull()
                            ->required()
                            ->default(true)
                            ->label(__('Is Active')),
                    ])->columns(2),

                Forms\Components\Section::make(__('Prizes'))->schema([
                    Forms\Components\Repeater::make('prizes')
                        ->relationship()
                        ->label(__('Wheel Prizes'))
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->label(__('Prize Name')),

                            Forms\Components\Select::make('type')
                                ->required()
                                ->options([
                                    'coupon' => 'Coupon',
                                    'discount' => 'Discount',
                                    'points' => 'Points',
                                    'product' => 'Product',
                                ])
                                ->label(__('Prize Type')),

                            Forms\Components\TextInput::make('value')
                                ->numeric()
                                ->label(__('Value')),

                            Forms\Components\Select::make('coupon_id')
                                ->relationship('coupon', 'code') // تأكد أن العلاقة موجودة بـ WheelPrize
                                ->label(__('Coupon'))
                                ->searchable(),

                            Forms\Components\Select::make('discount_id')
                                ->relationship('discount', 'name')
                                ->label(__('Discount'))
                                ->searchable(),

                            Forms\Components\TextInput::make('probability')
                                ->required()
                                ->numeric()
                                ->default(10)
                                ->minValue(1)
                                ->maxValue(100)
                                ->label(__('Probability %')),

                            Forms\Components\Toggle::make('is_available')
                                ->default(true)
                                ->label(__('Available')),

                            Forms\Components\TextInput::make('daily_limit')
                                ->numeric()
                                ->label(__('Daily Limit')),

                            Forms\Components\TextInput::make('total_limit')
                                ->numeric()
                                ->label(__('Total Limit')),
                        ])
                        ->columns(2)
                        ->defaultItems(1)
                        ->label(__('Wheel Prizes'))
                        ->columnSpanFull()
                ]),

                Forms\Components\Section::make(__('Date Range'))
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label(__('Start Date')),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->label(__('End Date')),
                    ])->columns(2),

                Forms\Components\Section::make(__('Spin Settings'))
                    ->schema([
                        Forms\Components\TextInput::make('spins_per_user')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->label(__('Max spins per user')),
                        Forms\Components\TextInput::make('spins_duration')
                            ->required()
                            ->numeric()
                            ->default(24)
                            ->minValue(1)
                            ->suffix(__('hours'))
                            ->label(__('Cooldown period')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('Name')),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label(__('Is Active')),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable()
                    ->label(__('Start Date')),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable()
                    ->label(__('End Date')),
                Tables\Columns\TextColumn::make('spins_per_user')
                    ->numeric()
                    ->sortable()
                    ->label(__('Spins Per User')),
                Tables\Columns\TextColumn::make('spins_duration')
                    ->numeric()
                    ->suffix(' '.__('hours'))
                    ->sortable()
                    ->label(__('Cooldown (hours)')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('Created At')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('Updated At')),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->columnSpanFull()
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->label(__('Only Active')),
                Tables\Filters\Filter::make('has_ended')
                    ->columnSpanFull()
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('end_date')->where('end_date', '<', now()))
                    ->label(__('Ended Wheels')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_prizes')
                    ->url(fn (Wheel $record): string => WheelPrizeResource::getUrl('index', ['wheel_id' => $record->id]))
                    ->label(__('Prizes'))
                    ->icon('heroicon-o-gift'),
                Tables\Actions\Action::make('view_spins')
                    ->url(fn (Wheel $record): string => WheelSpinResource::getUrl('index', ['wheel_id' => $record->id]))
                    ->label(__('Spins'))
                    ->icon('heroicon-o-arrow-path'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Delete selected')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWheels::route('/'),
            'create' => Pages\CreateWheel::route('/create'),
            'edit' => Pages\EditWheel::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Wheel');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Wheels');
    }

    public static function getNavigationLabel(): string
    {
        return __('wheel_of_fortune');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Wheels');
    }

    public static function getLabel(): ?string
    {
        return __('Wheel');
    }
}
