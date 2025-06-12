<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogCategoryResource\Pages;
use App\Models\BlogCategory;
use Closure;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BlogCategoryResource extends Resource
{
    use Translatable;

    protected static ?string $model = BlogCategory::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Blogs Management';

    public static function getNavigationLabel(): string
    {
        return __('Blog Categories');
    }

    public static function getLabel(): ?string
    {
        return __('Blog Category');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Blog Categories');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->columnSpanFull()
                        ->required()
                        ->maxLength(255)
                        ->label(__('Name')),

                    SelectTree::make('parent_id')
                        ->columnSpanFull()
                        ->label(__('Parent Category'))
                        ->searchable()
                        ->enableBranchNode()
                        ->relationship('parent', 'name', 'parent_id')
                        ->placeholder(__('Select a parent category'))
                        ->rules([
                            fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                if ($value === $get('id')) {
                                    $fail(__('A category cannot be its own parent.'));
                                }
                            },
                        ])
                        ->validationAttribute(__('Parent Category')),

                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull()
                        ->maxLength(65535)
                        ->columnSpanFull()
                        ->label(__('Description')),

                    Forms\Components\Checkbox::make('is_active')
                        ->columnSpanFull()
                        ->default(true)
                        ->label(__('Active')),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('Name')),
                TextColumn::make('parent.name')
                    ->badge()
                    ->label(__('Parent Category'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->label(__('Description')),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label(__('Active')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('Created At')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('Updated At')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('Edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('Delete'))
                    ->modalHeading(__('Delete Blog Category'))
                    ->modalDescription(__('Are you sure you want to delete this category?'))
                    ->modalSubmitActionLabel(__('Yes, delete'))
                    ->modalCancelActionLabel(__('No, cancel')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Delete Selected'))
                        ->modalHeading(__('Delete Selected Categories'))
                        ->modalDescription(__('Are you sure you want to delete the selected categories?'))
                        ->modalSubmitActionLabel(__('Yes, delete'))
                        ->modalCancelActionLabel(__('No, cancel')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogCategories::route('/'),
            'create' => Pages\CreateBlogCategory::route('/create'),
            'edit' => Pages\EditBlogCategory::route('/{record}/edit'),
        ];
    }
}
