<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PolicyResource\Pages;
use App\Models\Policy;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PolicyResource extends Resource
{
    protected static ?string $model = Policy::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?int $navigationSort = 100;

    public static function getNavigationLabel(): string
    {
        return __('policy.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('policy.navigation_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('policies');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('policy.navigation_group');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Policies')->tabs([
                Tab::make(__('policy.privacy_policy'))
                    ->columnSpanFull()
                    ->schema([
                        MarkdownEditor::make('privacy_policy_en')
                            ->label(__('policy.privacy_policy_en'))
                            ->columnSpanFull(),
                        MarkdownEditor::make('privacy_policy_ar')
                            ->label(__('policy.privacy_policy_ar'))
                            ->columnSpanFull(),
                    ]),
                Tab::make(__('policy.refund_policy'))
                    ->columnSpanFull()
                    ->schema([
                        MarkdownEditor::make('refund_policy_en')
                            ->label(__('policy.refund_policy_en'))
                            ->columnSpanFull(),
                        MarkdownEditor::make('refund_policy_ar')
                            ->label(__('policy.refund_policy_ar'))
                            ->columnSpanFull(),
                    ]),
                Tab::make(__('policy.terms_of_service'))
                    ->columnSpanFull()
                    ->schema([
                        MarkdownEditor::make('terms_of_service_en')
                            ->label(__('policy.terms_of_service_en'))
                            ->columnSpanFull(),
                        MarkdownEditor::make('terms_of_service_ar')
                            ->label(__('policy.terms_of_service_ar'))
                            ->columnSpanFull(),
                    ]),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('privacy_policy_en')
                    ->label(__('policy.privacy_policy_en'))
                    ->limit(50),

                TextColumn::make('privacy_policy_ar')
                    ->label(__('policy.privacy_policy_ar'))
                    ->limit(50),

                TextColumn::make('refund_policy_en')
                    ->label(__('policy.refund_policy_en'))
                    ->limit(50),

                TextColumn::make('refund_policy_ar')
                    ->label(__('policy.refund_policy_ar'))
                    ->limit(50),

                TextColumn::make('terms_of_service_en')
                    ->label(__('policy.terms_of_service_en'))
                    ->limit(50),

                TextColumn::make('terms_of_service_ar')
                    ->label(__('policy.terms_of_service_ar'))
                    ->limit(50)
            ])
            ->actions([
               EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPolicies::route('/'),
            'edit' => Pages\EditPolicy::route('/{record}/edit'),
        ];
    }
}
