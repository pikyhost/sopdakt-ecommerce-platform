<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\BlogUserLikeResource\Pages;
use App\Models\BlogUserLike;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class BlogUserLikeResource extends Resource
{
    protected static ?string $model = BlogUserLike::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-thumb-up';

    protected static ?string $navigationGroup = 'Blogs Management';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('User Likes');
    }

    public static function getModelLabel(): string
    {
        return __('Favorite Article');
    }

    public static function getLabel(): ?string
    {
        return __('Favorite Article');
    }

    public static function getBreadcrumb(): string
    {
        return __('Favorite Articles');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Favorite Articles');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('blog.title')
                    ->label(__('Article Title'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('User Name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Added Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label(__('User'))
                    ->relationship('user', 'name')
                    ->placeholder(__('All Users'))
                    ->visible(fn () => auth()->user()->hasRole(UserRole::SuperAdmin->value)),

                SelectFilter::make('blog_id')
                    ->label(__('Article'))
                    ->relationship('blog', 'title')
                    ->placeholder(__('All Articles'))
                    ->visible(fn () => auth()->user()->hasRole(UserRole::SuperAdmin->value)),
            ], Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(2);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogUserLikes::route('/'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
