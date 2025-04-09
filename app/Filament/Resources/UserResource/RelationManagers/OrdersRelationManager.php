<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->copyable()
                    ->formatStateUsing(fn($state) => '#' . $state)
                    ->label(__('Number'))
                    ->searchable(),

                TextColumn::make('tracking_number')
                    ->copyable()
                    ->placeholder('-')
                    ->label(__('Tracking Number'))
                    ->searchable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('user.name')
                    ->formatStateUsing(function ($record) {
                        return $record->user->name.' (#'.$record->user_id.')';
                    })
                    ->label(__('User Name'))
                    ->searchable()
                    ->placeholder('-')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contact.name')
                    ->formatStateUsing(function ($record) {
                        return $record->contact->name.' (#'.$record->contact_id.')';
                    })
                    ->label(__('Contact Name'))
                    ->searchable()
                    ->placeholder('-')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('user.phone')
                    ->iconColor('primary')
                    ->icon('heroicon-o-phone')
                    ->label(__('User Phone Number'))
                    ->placeholder(__('No phone number saved'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.second_phone')
                    ->label(__('Second Phone Number'))
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('contact.phone')
                    ->iconColor('primary')
                    ->icon('heroicon-o-phone')
                    ->label(__('Contact Phone Number'))
                    ->placeholder(__('No phone number saved'))
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
