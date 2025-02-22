<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LandingPageOrder;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;

class OrderResource extends Resource
{
    protected static ?string $model = LandingPageOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Orders';
    protected static ?string $navigationLabel = 'Orders';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Order ID')->sortable(),
                TextColumn::make('landingPage.name')->label('Landing Page')->sortable(),
                TextColumn::make('governorate.name')->label('Governorate')->sortable(),
                TextColumn::make('country.name')->label('Country')->sortable(),
                TextColumn::make('shippingType.name')->label('Shipping Type')->sortable(),
                TextColumn::make('landingPageBundle.name')->label('Landing Page Bundle')->sortable(),
                TextColumn::make('name')->label('Customer Name')->sortable()->searchable(),
                TextColumn::make('phone')->label('Phone Number')->sortable()->searchable(),
                TextColumn::make('another_phone')->label('Alternate Phone')->sortable()->searchable(),
                TextColumn::make('address')->label('Address')->sortable()->limit(50),
                TextColumn::make('quantity')->label('Quantity')->sortable(),
                TextColumn::make('subtotal')->label('Subtotal')->money('USD')->sortable(),
                TextColumn::make('shipping_cost')->label('Shipping Cost')->money('USD')->sortable(),
                TextColumn::make('total')->label('Total Price')->money('USD')->sortable(),
                TextColumn::make('status')->label('Status')->badge()->sortable(),
                TextColumn::make('notes')->label('Notes')->limit(100),
                TextColumn::make('created_at')->label('Order Date')->dateTime('M d, Y H:i A')->sortable(),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/')
        ];
    }
}
