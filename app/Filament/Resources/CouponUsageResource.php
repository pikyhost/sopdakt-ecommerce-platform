<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponUsageResource\Pages;
use App\Models\CouponUsage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponUsageResource extends Resource
{
    protected static ?string $model = CouponUsage::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $modelLabel = 'Coupon Usage';

    protected static ?string $navigationLabel = 'Coupon Usages';

    protected static ?string $navigationGroup = 'Discounts';

    protected static ?int $navigationSort = 3;

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

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('Customer'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.id')
                    ->label(__('Order ID'))
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => OrderResource::getUrl('edit', ['record' => $record->order_id])),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Used At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('coupon_id')
                    ->label(__('Coupon'))
                    ->relationship('coupon', 'code')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('Customer'))
                    ->relationship('user', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListCouponUsages::route('/'),
            'create' => Pages\CreateCouponUsage::route('/create'),
            'view' => Pages\ViewCouponUsage::route('/{record}'),
        ];
    }
}
