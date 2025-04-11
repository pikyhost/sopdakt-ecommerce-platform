<?php

namespace App\Filament\Resources\GovernorateResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static bool $isLazy = false;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return '';
    }

    protected static function getModelLabel(): ?string
    {
        return '';
    }

    protected static function getPluralModelLabel(): ?string
    {
        return '';
    }

    protected static function getPluralRecordLabel(): ?string
    {
        return '';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->header(null)
            ->headerActions([
                Action::make('back')
                    ->color('primary')
                    ->label(__('Back to previous page'))
                    ->icon(function () {
                        return app()->getLocale() == 'en' ? 'heroicon-m-arrow-right' : 'heroicon-m-arrow-left';
                    })
                    ->iconPosition(IconPosition::After)
                    ->color('gray')
                    ->url(url()->previous())
                    ->hidden(fn () => url()->previous() === url()->current()), // Optionally hide if same page
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->copyable()
                    ->formatStateUsing(fn($state) => '#' . $state)
                    ->label(__('Number'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('shippingType.name')
                    ->label(__('Shipping Type'))
                    ->searchable()
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label(__('Payment Method'))
                    ->searchable()
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('coupon.id')
                    ->label(__('Coupon ID'))
                    ->searchable()
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('shipping_cost')
                    ->label(__('Shipping Cost'))
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tax_percentage')
                    ->label(__('Tax Percentage'))
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tax_amount')
                    ->label(__('Tax Amount'))
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label(__('Subtotal'))
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label(__('Total'))
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
