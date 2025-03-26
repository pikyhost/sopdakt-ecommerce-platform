<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PolicyResource\Pages;
use App\Models\Policy;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\MarkdownEditor;

class PolicyResource extends Resource
{
    protected static ?string $model = Policy::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('policy.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('policy.model_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('policy.navigation_group');
    }

    public static function getLabel(): ?string
    {
        return __('policy.label');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Policies')->tabs([
                Tab::make(__('policy.privacy_policy'))
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
            ]),
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
