<?php

namespace App\Filament\Resources;

use App\Filament\Exports\GovernorateExporter;
use App\Filament\Imports\CountryImporter;
use App\Filament\Imports\GovernorateImporter;
use App\Filament\Resources\GovernorateResource\Pages;
use App\Filament\Resources\GovernorateResource\RelationManagers\OrdersRelationManager;
use App\Models\Governorate;
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
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\ExportAction;

class GovernorateResource extends Resource
{
    use Translatable;
    use HasMakeCostZeroAction;

    protected static ?string $model = Governorate::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function getNavigationLabel(): string
    {
        return __('governorates'); // Translated to "Governorates"
    }

    public static function getModelLabel(): string
    {
        return __('governorate'); // Translated to "Governorate"
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('governorates'); // Translated to "Governorates"
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Shipping Management'); //Products Attributes Management
    }

    public static function getLabel(): ?string
    {
        return __('governorate'); // Translated to "Governorate"
    }

    public static function getPluralModelLabel(): string
    {
        return __('governorates'); // Translated to "Governorates"
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    // ->unique('cities', 'name', fn ($record) => $record) // Explicitly check the unique rule for the 'name' field
                    ->rules([
                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            $recordId = $get('id'); // Get the current record ID
                            $countryId = $get('country_id'); // Get the selected country ID

                            // Check if any locale in the JSON `name` column contains the given value
                            $exists = DB::table('governorates')
                                ->where(function ($query) use ($value) {
                                    foreach (config('app.supported_locales') as $locale) {
                                        $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.\"{$locale}\"')) = ?", [$value]);
                                    }
                                })
                                ->where('country_id', $countryId)
                                ->when($recordId, fn($query) => $query->where('id', '!=', $recordId)) // Ignore current record
                                ->exists();

                            if ($exists) {
                                $fail(__('validation.unique', ['attribute' => __('governorate.name')]));
                            }
                        },
                    ])
                    ->required()
                    ->maxLength(255)
                    ->label(__('name')),
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required()
                    ->label(__('country_name')),

                Forms\Components\TextInput::make('cost')
                    ->label(__('Shipping Cost'))
                    ->required()
                    ->numeric()
                    ->default(0),

                Forms\Components\TextInput::make('shipping_estimate_time')
                    ->label(__('shipping_cost.shipping_estimate_time'))
                    ->maxLength(255)
                    ->default('0-0'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(GovernorateImporter::class),
                ExportAction::make()
                    ->exporter(GovernorateExporter::class)
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('name')),
                Tables\Columns\TextColumn::make('country.name')
                    ->searchable()
                    ->label(__('country_name')),

                Tables\Columns\TextColumn::make('cost')
                    ->label(__('Shipping Cost'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('shipping_estimate_time')
                    ->label(__('shipping_cost.shipping_estimate_time'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('updated_at')),
            ])
            ->recordUrl(false)
            ->filters([
                Tables\Filters\SelectFilter::make('country_id')
                    ->columnSpanFull()
                    ->label(__('country'))
                    ->relationship('country', 'name')
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
                Section::make()->schema([
                    TextEntry::make('name')
                        ->label(__('name')),

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
            OrdersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGovernorates::route('/'),
            'view'  => Pages\ViewGovernorate::route('/{record}'),
            'orders' => Pages\ManageGovernorateOrders::route('/{record}/orders')
        ];
    }
}
