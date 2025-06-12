<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponUsageResource\Pages;
use App\Models\CouponUsage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponUsageResource extends Resource
{
    protected static ?string $model = CouponUsage::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $modelLabel = 'Coupon Usage';

    protected static ?string $navigationLabel = 'Coupon Usages';

    public static function getModelLabel(): string
    {
        return __('Coupon Usages');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Coupon Usages');
    }

    public static function getNavigationLabel(): string
    {
        return __('Coupon Usages');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Coupon Usages');
    }

    public static function getLabel(): ?string
    {
        return __('Coupon Usage');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Usage Details')
                    ->schema([
                        Forms\Components\Select::make('coupon_id')
                            ->label(__('Coupon'))
                            ->relationship('coupon', 'code')
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('user_id')
                            ->label(__('Customer'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('order_id')
                            ->label(__('Order'))
                            ->relationship('order', 'id')
                            ->searchable()
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('coupon.code')
                    ->label(__('Coupon Code'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(__('Customer'))
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        // If user_id is set and user exists, return the user's name
                        if ($record->user) {
                            return $record->user->name;
                        }

                        // For guest users, try to fetch guest name from related model (e.g., Order or Cart)
                        if ($record->order) {
                            return $record->order->contact->name; // Adjust based on your model
                        }

                        // Fallback for guests with no name (e.g., "Guest" or session_id)
                        return 'Guest';
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Used At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCouponUsages::route('/'),
        ];
    }
}
