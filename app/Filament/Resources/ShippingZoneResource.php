<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingZoneResource\Pages;
use App\Models\ShippingZone;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class ShippingZoneResource extends Resource
{
    use Translatable;

    protected static ?string $model = ShippingZone::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    public static function getNavigationLabel(): string
    {
        return __('navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Shipping & Countries'); //Products Attributes Management
    }

    public static function getModelLabel(): string
    {
        return __('model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('plural_model_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('name'))
                    ->required()
                    ->maxLength(255)
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            $recordId = $get('id'); // Get the current record ID

                            // Check if any locale in the JSON `name` column contains the given value
                            $exists = DB::table('shipping_zones')
                                ->where(function ($query) use ($value) {
                                    foreach (config('app.supported_locales') as $locale) {
                                        $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.\"{$locale}\"')) = ?", [$value]);
                                    }
                                })
                                ->when($recordId, fn ($query) => $query->where('id', '!=', $recordId)) // Ignore current record
                                ->exists();

                            if ($exists) {
                                $fail(__('validation.unique', ['attribute' => __('shipping_zone.name')]));
                            }
                        },
                    ]),

                Forms\Components\Select::make('governorates')
                    ->preload()
                    ->label(__('governorates'))
                    ->relationship('governorates', 'name')
                    ->multiple(),

                TextInput::make('cost')
                    ->label(__('shipping_cost.cost'))
                    ->required()
                    ->numeric()
                    ->default(0),

                TextInput::make('shipping_estimate_time')
                    ->label(__('shipping_cost.shipping_estimate_time'))
                    ->required()
                    ->maxLength(255)
                    ->default('0-0'),

                Forms\Components\Textarea::make('description')
                    ->nullable()
                    ->label(__('Description')),

            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('governorates.name')
                    ->placeholder('-')
                    ->label(__('governorates'))
                    ->limitList(4)
                    ->badge(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('shipping_zone.description'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageShippingZones::route('/'),
        ];
    }
}
