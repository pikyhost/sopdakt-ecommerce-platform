<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PopupResource\Pages;
use App\Models\Popup;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Route;

class PopupResource extends Resource
{
    use Translatable;

    protected static ?string $model = Popup::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-pointing-out';

    public static function getNavigationLabel(): string
    {
        return __('Popups');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Content Management');
    }

    public static function getModelLabel(): string
    {
        return __('Popup');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Popups');
    }

    public static function getLabel(): ?string
    {
        return __('Popup');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Popups');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    // Visual content
                    Forms\Components\FileUpload::make('image_path')
                        ->columnSpanFull()
                        ->label(__('Image'))
                        ->image()
                        ->helperText(__('image_path_helper')),

                    // Basic info
                    Forms\Components\TextInput::make('title')
                        ->label(__('Title'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->helperText(__('title_helper')),

                    Forms\Components\Textarea::make('description')
                        ->label(__('Description'))
                        ->required()
                        ->columnSpanFull()
                        ->helperText(__('description_helper')),

                    // CTA
                    Forms\Components\TextInput::make('cta_text')
                        ->label(__('CTA Text'))
                        ->required()
                        ->maxLength(255)
                        ->helperText(__('cta_text_helper')),

                    Forms\Components\TextInput::make('cta_link')
                        ->label(__('CTA Link'))
                        ->required()
                        ->maxLength(255)
                        ->helperText(__('cta_link_helper')),

//                    // Timing
//                    Forms\Components\TextInput::make('delay_seconds')
//                        ->label(__('Delay Seconds'))
//                        ->required()
//                        ->numeric()
//                        ->default(5)
//                        ->helperText(__('delay_seconds_helper')),
//
//                    Forms\Components\TextInput::make('duration_seconds')
//                        ->label(__('Display Duration'))
//                        ->numeric()
//                        ->helperText(__('duration_seconds_helper')),
//
//                    Forms\Components\TextInput::make('dont_show_again_days')
//                        ->label(__('Hide for (days) when closed with "Don\'t show again"'))
//                        ->default(30)
//                        ->numeric()
//                        ->helperText(__('dont_show_again_days_helper')),

                    Forms\Components\TextInput::make('popup_order')
                        ->label(__('Popup Order'))
                        ->numeric()
                        ->default(0)
                        ->helperText(__('popup_order_helper')),

//                    Forms\Components\TextInput::make('show_interval_minutes')
//                        ->label(__('Interval Between Displays (minutes)'))
//                        ->numeric()
//                        ->default(60)
//                        ->helperText(__('show_interval_minutes_helper')),

                    // Display logic
                    Forms\Components\Select::make('display_rules')
                        ->label(__('Display Rules'))
                        ->options([
                            'all_pages' => 'All Pages',
                            'specific_pages' => 'Specific Pages',
                            'page_group' => 'Page Group',
                            'all_except_specific' => 'All Pages EXCEPT Specific',
                            'all_except_group' => 'All Pages EXCEPT Group',
                        ])
                        ->live()
                        ->required()
                        ->helperText(__('display_rules_helper')),


                    Select::make('specific_pages')
                        ->label(__('Page Rules'))
                        ->multiple()
                        ->searchable()
                        ->visible(fn ($get) => in_array($get('display_rules'), [
                            'specific_pages', 'page_group', 'all_except_specific', 'all_except_group'
                        ]))
                        ->options(function () {
                            return collect(Route::getRoutes())
                                ->filter(fn ($route) =>
                                    in_array('GET', $route->methods()) &&
                                    !str_contains($route->uri(), '{') && // Exclude dynamic routes
                                    $route->uri() !== '/' && // Exclude homepage
                                    !preg_match('#^(admin|client|api|_debugbar|livewire|sanctum|storage)#', $route->uri())
                                )
                                ->mapWithKeys(fn ($route) => [$route->uri() => '/' . $route->uri()])
                                ->sort()
                                ->toArray();
                        })
                        ->getSearchResultsUsing(function (string $search): array {
                            return collect(Route::getRoutes())
                                ->filter(fn ($route) =>
                                    in_array('GET', $route->methods()) &&
                                    !str_contains($route->uri(), '{') &&
                                    $route->uri() !== '/' &&
                                    str_contains($route->uri(), $search) &&
                                    !preg_match('#^(admin|client|api|_debugbar|livewire|sanctum|storage)#', $route->uri())
                                )
                                ->mapWithKeys(fn ($route) => [$route->uri() => '/' . $route->uri()])
                                ->sort()
                                ->toArray();
                        })
                        ->getOptionLabelsUsing(function (array $values): array {
                            return collect($values)
                                ->mapWithKeys(fn ($uri) => [$uri => '/' . $uri])
                                ->toArray();
                        })
                        ->dehydrateStateUsing(fn ($state) => json_encode($state))
                        ->afterStateHydrated(fn ($state, callable $set) => $set('specific_pages', json_decode($state ?? '[]')))
                        ->nullable()
                        ->columnSpanFull()
                        ->helperText(__('specific_pages_helper')),

                    Forms\Components\Checkbox::make('email_needed')
                        ->label(__('Email needed'))
                        ->helperText(__('email_needed_helper')),

                    Forms\Components\Checkbox::make('is_active')
                        ->label(__('Is Active'))
                        ->helperText(__('is_active_helper')),
                ])->columns(2)
            ]);
    }

    public static function getPublicPageOptions(): array
    {
        return collect(Route::getRoutes())
            ->filter(fn ($route) =>
                $route->methods() === ['GET'] &&
                $route->uri() !== '/' &&
                !str_starts_with($route->uri(), 'admin') &&
                !str_starts_with($route->uri(), 'client') &&
                !str_contains($route->uri(), '{') // skip dynamic segments like {id}
            )
            ->mapWithKeys(fn ($route) => [$route->uri() => '/' . $route->uri()])
            ->sort()
            ->all();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(),

                Tables\Columns\ImageColumn::make('image_path')
                    ->label(__('Image')),

                Tables\Columns\TextColumn::make('cta_text')
                    ->label(__('CTA Text'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('cta_link')
                    ->label(__('CTA Link'))
                    ->searchable(),

//                Tables\Columns\TextColumn::make('delay_seconds')
//                    ->label(__('Delay Seconds'))
//                    ->numeric()
//                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Is Active'))
                    ->boolean(),


                Tables\Columns\IconColumn::make('email_needed')
                    ->label(__('Email needed?'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('display_rules')
                    ->label(__('Display Rules')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('Edit')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Delete Selected')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPopups::route('/'),
            'create' => Pages\CreatePopup::route('/create'),
            'edit' => Pages\EditPopup::route('/{record}/edit'),
        ];
    }
}
