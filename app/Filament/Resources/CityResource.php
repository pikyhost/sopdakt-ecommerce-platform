<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\Pages\ManageCityOrders;
use App\Filament\Resources\CountryResource\RelationManagers\OrdersRelationManager;
use App\Models\City;
use App\Traits\HasMakeCostZeroAction;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class CityResource extends Resource
{
    use Translatable;
    use HasMakeCostZeroAction;

    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function getNavigationLabel(): string
    {
        return __('cities'); // Translated to "Governorates"
    }

    public static function getModelLabel(): string
    {
        return __('cities'); // Translated to "Governorate"
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('cities'); // Translated to "Governorates"
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Shipping Management'); //Products Attributes Management
    }

    public static function getLabel(): ?string
    {
        return __('cities'); // Translated to "Governorate"
    }

    public static function getPluralModelLabel(): string
    {
        return __('cities'); // Translated to "Governorates"
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label(__('name'))
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            $recordId = $get('id'); // Get the current record ID
                            $governorateId = $get('governorate_id'); // Get the selected governorate ID

                            // Check if any locale in the JSON `name` column contains the given value
                            $exists = DB::table('cities')
                                ->where(function ($query) use ($value) {
                                    foreach (config('app.supported_locales') as $locale) {
                                        $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.\"{$locale}\"')) = ?", [$value]);
                                    }
                                })
                                ->where('governorate_id', $governorateId)
                                ->when($recordId, fn ($query) => $query->where('id', '!=', $recordId)) // Ignore current record
                                ->exists();

                            if ($exists) {
                                $fail(__('validation.unique', ['attribute' => Str::afterLast($attribute, '.')]));
                            }
                        },
                    ]),

                Forms\Components\Select::make('governorate_id')
                    ->relationship('governorate', 'name')
                    ->required()
                    ->label(__('governorate_name')),

                Forms\Components\TextInput::make('cost')
                    ->label(__('Shipping Cost'))
                    ->required()
                    ->numeric()
                    ->default(0),

                Forms\Components\TextInput::make('shipping_estimate_time')
                    ->label(__('shipping_cost.shipping_estimate_time'))
                    ->maxLength(255)
                    ->default('0-0'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(false)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('name')),
                Tables\Columns\TextColumn::make('governorate.name')
                    ->searchable()
                    ->label(__('governorate_name')),
                Tables\Columns\TextColumn::make('cost')
                    ->label(__('Shipping Cost'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('shipping_estimate_time')
                    ->label(__('Shipping Cost'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('created_at')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('updated_at')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('governorate_id')
                    ->columnSpanFull()
                    ->label(__('governorate'))
                    ->relationship('governorate', 'name')
            ], Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('delete')),
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('delete_bulk')),
                    self::makeCostZeroBulkAction(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('name'))
                            ->weight(FontWeight::Bold)
                            ->columnSpanFull(),

                        TextEntry::make('governorate.name')
                            ->label(__('governorate_name')),

                        TextEntry::make('cost')
                            ->label(__('Shipping Cost')),

                        TextEntry::make('shipping_estimate_time')
                            ->label(__('shipping_cost.shipping_estimate_time')),

                        TextEntry::make('created_at')
                            ->label(__('created_at'))
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label(__('updated_at'))
                            ->dateTime(),
                    ])->columns(2)
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\CityResource\RelationManagers\OrdersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCities::route('/'),
            'view'  => Pages\ViewCity::route('/{record}'),
            'orders' => ManageCityOrders::route('/{record}/orders')
        ];
    }
}
