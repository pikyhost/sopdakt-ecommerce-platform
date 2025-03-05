<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name'),
                Forms\Components\Select::make('contact_id')
                    ->relationship('contact', 'name'),
                Forms\Components\Select::make('shipping_type_id')
                    ->relationship('shippingType', 'name')
                    ->required(),
                Forms\Components\Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->required(),
                Forms\Components\Select::make('coupon_id')
                    ->relationship('coupon', 'id'),
                Forms\Components\TextInput::make('shipping_cost')
                    ->numeric(),
                Forms\Components\TextInput::make('tax_percentage')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('tax_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->placeholder('-')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact.name')
                    ->searchable()
                    ->placeholder('-')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                ->searchable(),
                Tables\Columns\TextColumn::make('shippingType.name')
                    ->searchable()
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->searchable()
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('coupon.id')
                    ->searchable()
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_cost')
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax_percentage')
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax_amount')
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
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
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Order from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Order until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ], Tables\Enums\FiltersLayout::Modal)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    ...collect(OrderStatus::cases())->map(fn ($status) =>
                    Tables\Actions\Action::make($status->value)
                        ->label(__($status->getLabel()))
                        ->icon($status->getIcon())
                        ->color($status->getColor())
                        ->action(fn ($record) => $record->update(['status' => $status->value]))
                    )->toArray(),

                    Tables\Actions\DeleteAction::make(),
                ])->label(__('Actions'))
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ...collect(OrderStatus::cases())->map(fn ($status) =>
                    Tables\Actions\BulkAction::make($status->value)
                        ->label(__($status->getLabel()))
                        ->icon($status->getIcon())
                        ->color($status->getColor())
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['status' => $status->value])))
                    )->toArray(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
