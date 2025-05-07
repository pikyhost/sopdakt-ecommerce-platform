<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Services\Filament\BlogActionsService;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BlogResource extends Resource
{
    use Translatable;

    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Blogs Management';

    public static function getNavigationLabel(): string
    {
        return __('Blogs');
    }

    public static function getLabel(): ?string
    {
        return __('Blog');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Blogs');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'content', 'author.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('Author') => $record->author->name,
            __('Category') => $record->category->name,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->label(__('Title')),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->label(__('Slug')),
                            Forms\Components\MarkdownEditor::make('content')
                                ->required()
                                ->columnSpanFull()
                                ->label(__('Content')),
                            Forms\Components\Toggle::make('is_active')
                                ->default(false)
                                ->label(__('Active')),
                        ])->columns(1),
                    ])->columnSpan(2),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make([
                            Forms\Components\SpatieMediaLibraryFileUpload::make('main_blog_image')
                                ->live()
                                ->image()
                                ->label(__('Featured Image'))
                                ->collection('main_blog_image')
                                ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\SpatieMediaLibraryFileUpload $component) {
                                    $livewire->validateOnly($component->getStatePath());
                                }),

                            SelectTree::make('blog_category_id')
                                ->placeholder(__('Select Category'))
                                ->label(__('Category'))
                                ->searchable()
                                ->enableBranchNode()
                                ->relationship('category', 'name', 'parent_id'),
                        ]),

                        Forms\Components\Section::make([
                            Forms\Components\Select::make('tags')
                                ->multiple()
                                ->relationship('tags', 'name')
                                ->preload()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->unique('tags', 'name')
                                        ->label(__('Name')),
                                ])
                                ->label(__('Tags')),
                            Forms\Components\DatePicker::make('published_at')
                                ->required()
                                ->default(now())
                                ->label(__('Publish Date')),
                        ]),
                    ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('main_blog_image')
                    ->label(__('Image'))
                    ->collection('main_blog_image'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->label(__('Title')),
                TextColumn::make('category.parent.name')
                    ->badge()
                    ->label(__('Parent Category'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->label(__('Category')),
                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->label(__('Author')),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label(__('Active')),
                Tables\Columns\TextColumn::make('tags.name')
                    ->placeholder('-')
                    ->badge()
                    ->color('warning')
                    ->label(__('Tags')),
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
            ->filtersFormColumns(6)
            ->filters([
                TernaryFilter::make('is_active')
                    ->native(false)
                    ->placeholder(__('Select Status'))
                    ->label('')
                    ->columnSpan(['default' => 6, 'sm' => 6, 'md' => 6, 'lg' => 3, 'xl' => 3, '2xl' => 3]),

                Filter::make('category')
                    ->columnSpan(['default' => 6, 'sm' => 6, 'md' => 6, 'lg' => 3, 'xl' => 3, '2xl' => 3])
                    ->form([
                        SelectTree::make('blog_category_id')
                            ->placeholder(__('Search for a Category...'))
                            ->enableBranchNode()
                            ->hiddenLabel()
                            ->relationship('category', 'name', 'parent_id')
                            ->searchable(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['blog_category_id'])) {
                            $selectedCategoryId = $data['blog_category_id'];

                            // Check if the selected category has children
                            $hasChildren = BlogCategory::where('parent_id', $selectedCategoryId)->exists();

                            if ($hasChildren) {
                                // If it's a parent category, get all its descendants
                                $categoryIds = self::getCategoryWithDescendants($selectedCategoryId);
                                $query->whereIn('blog_category_id', $categoryIds);
                            } else {
                                // If it's a child category, just filter for that category
                                $query->where('blog_category_id', $selectedCategoryId);
                            }
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (empty($data['blog_category_id'])) {
                            return null;
                        }

                        $category = BlogCategory::find($data['blog_category_id']);
                        $categoryName = $category->name ?? __('Unknown Category');

                        // Check if it's a parent category
                        $isParent = BlogCategory::where('parent_id', $data['blog_category_id'])->exists();

                        if ($isParent) {
                            return __('Showing blogs in category and subcategories:') . " {$categoryName}";
                        }

                        return __('Showing blogs in category:') . " {$categoryName}";
                    }),

                Tables\Filters\SelectFilter::make('author_id')
                    ->native(false)
                    ->label('')
                    ->placeholder(__('Select Author'))
                    ->relationship('author', 'name')
                    ->columnSpan(['default' => 6, 'sm' => 6, 'md' => 6, 'lg' => 3, 'xl' => 3, '2xl' => 3]),

                Tables\Filters\SelectFilter::make('tags')
                    ->multiple()
                    ->preload()
                    ->native(false)
                    ->label('')
                    ->placeholder(__('Select Tags'))
                    ->relationship('tags', 'name')
                    ->columnSpan(['default' => 6, 'sm' => 6, 'md' => 6, 'lg' => 3, 'xl' => 3, '2xl' => 3]),
            ], Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Action::make('like')
                        ->visible(fn() => auth()->check())
                        ->label(fn(Blog $record) => BlogActionsService::getLikeActionLabel($record))
                        ->icon(fn(Blog $record) => BlogActionsService::getLikeActionIcon($record))
                        ->color(fn(Blog $record) => BlogActionsService::getLikeActionColor($record))
                        ->action(fn(Blog $record) => BlogActionsService::toggleLikeBlog($record)),
                    Tables\Actions\EditAction::make()
                        ->label(__('Edit')),
                    Tables\Actions\DeleteAction::make()
                        ->label(__('Delete'))
                        ->modalHeading(__('Delete Blog'))
                        ->modalDescription(__('Are you sure you want to delete this blog?'))
                        ->modalSubmitActionLabel(__('Yes, delete'))
                        ->modalCancelActionLabel(__('No, cancel')),
                ])
                    ->label(__('Actions'))
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Delete Selected'))
                        ->modalHeading(__('Delete Selected Blogs'))
                        ->modalDescription(__('Are you sure you want to delete the selected blogs?'))
                        ->modalSubmitActionLabel(__('Yes, delete'))
                        ->modalCancelActionLabel(__('No, cancel')),
                ]),
            ]);
    }

    protected static function getCategoryWithDescendants($categoryId): \Illuminate\Support\Collection
    {
        $categoryIds = collect([$categoryId]);

        $getDescendantIds = function ($parentId) use (&$getDescendantIds) {
            return BlogCategory::where('parent_id', $parentId)
                ->pluck('id')
                ->flatMap(function ($id) use ($getDescendantIds) {
                    return collect([$id])->merge($getDescendantIds($id));
                });
        };

        return $categoryIds->merge($getDescendantIds($categoryId));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
            'view' => Pages\ViewBlog::route('/{record}'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('Basic Information'))
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        \Filament\Infolists\Components\Split::make([
                            Grid::make(2)->schema([
                                Group::make([
                                    TextEntry::make('title'),
                                    TextEntry::make('category.name'),
                                ]),
                                Group::make([
                                    TextEntry::make('author.name'),
                                    IconEntry::make('is_active')->boolean(),
                                ]),
                            ]),
                            SpatieMediaLibraryImageEntry::make('main_blog_image')
                                ->collection('main_blog_image')
                                ->simpleLightbox()
                                ->hiddenLabel()
                                ->grow(false),
                        ])->from('xl'),
                    ]),

                Section::make(__('Timestamps'))
                    ->icon('heroicon-o-clock')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('Created At'))
                            ->dateTime('D, M j, Y \a\t g:i A'),
                        TextEntry::make('updated_at')
                            ->label(__('Updated At'))
                            ->dateTime('D, M j, Y \a\t g:i A'),
                        TextEntry::make('published_at')
                            ->label(__('Published At'))
                            ->dateTime('D, M j, Y \a\t g:i A'),
                    ])->columns(3),

                Section::make(__('Content'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('content')
                            ->label(__('Content'))
                            ->placeholder(__('No content available'))
                            ->prose()
                            ->markdown()
                            ->hiddenLabel(),
                    ])->collapsible(),
            ]);
    }
}
