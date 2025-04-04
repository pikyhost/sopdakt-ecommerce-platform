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
                            ->required()
                            ->columnSpanFull(),
                        MarkdownEditor::make('privacy_policy_ar')
                            ->label(__('policy.privacy_policy_ar'))
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Tab::make(__('policy.refund_policy'))
                    ->columnSpanFull()
                    ->schema([
                        MarkdownEditor::make('refund_policy_en')
                            ->label(__('policy.refund_policy_en'))
                            ->required()
                            ->columnSpanFull(),
                        MarkdownEditor::make('refund_policy_ar')
                            ->label(__('policy.refund_policy_ar'))
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Tab::make(__('policy.terms_of_service'))
                    ->columnSpanFull()
                    ->schema([
                        MarkdownEditor::make('terms_of_service_en')
                            ->label(__('policy.terms_of_service_en'))
                            ->required()
                            ->columnSpanFull(),
                        MarkdownEditor::make('terms_of_service_ar')
                            ->label(__('policy.terms_of_service_ar'))
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Tab::make(__('policy.about_us'))
                    ->columnSpanFull()
                    ->schema([
                        MarkdownEditor::make('about_us_en')
                            ->label(__('policy.about_us_en'))
                            ->required()
                            ->columnSpanFull(),
                        MarkdownEditor::make('about_us_ar')
                            ->label(__('policy.about_us_ar'))
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Tab::make(__('policy.contact_us'))
                    ->columnSpanFull()
                    ->schema([
                        MarkdownEditor::make('contact_us_en')
                            ->label(__('policy.contact_us_en'))
                            ->required()
                            ->columnSpanFull(),
                        MarkdownEditor::make('contact_us_ar')
                            ->label(__('policy.contact_us_ar'))
                            ->required()
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
                    ->limit(50),

                TextColumn::make('about_us_en')
                    ->label(__('policy.about_us_en'))
                    ->limit(50),

                TextColumn::make('about_us_ar')
                    ->label(__('policy.about_us_ar'))
                    ->limit(50),

                TextColumn::make('contact_us_en')
                    ->label(__('policy.contact_us_en'))
                    ->limit(50),

                TextColumn::make('contact_us_ar')
                    ->label(__('policy.contact_us_ar'))
                    ->limit(50),
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
