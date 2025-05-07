<?php

namespace App\Filament\Pages;

use App\Models\Tag;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Tags extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    protected static string $view = 'filament.pages.tags';

    protected static ?string $navigationGroup = 'Blogs Management';

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Tags');
    }

    public static function getNavigationLabel(): string
    {
        return __('Tags');
    }

    public static function getLabel(): ?string
    {
        return __('Tag');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Tags');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Add New Tag'))
                    ->schema([
                        TextInput::make('name_en')
                            ->columnSpanFull()
                            ->label(__('In English'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn (string $state): string => ucwords($state)),

                        TextInput::make('name_ar')
                            ->columnSpanFull()
                            ->label(__('In Arabic'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                    ]),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $formData = $this->form->getState();

        Tag::create([
            'name_en' => $formData['name_en'],
            'name_ar' => $formData['name_ar'],
        ]);

        $this->form->fill([]);

        Notification::make()
            ->title(__('Saved successfully'))
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Tags'))
            ->description(__('Manage blog tags'))
            ->query(Tag::latest())
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name_en')
                    ->label(__('Tag Name (English)'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name_ar')
                    ->label(__('Tag Name (Arabic)'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label(__('Deleted At'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ViewAction::make()->infolist(function () {
                    return [
                        TextEntry::make('id')->label(__('ID'))->inlineLabel(),
                        TextEntry::make('name_en')->label(__('Tag Name (English)'))->inlineLabel(),
                        TextEntry::make('name_ar')->label(__('Tag Name (Arabic)'))->inlineLabel(),
                        TextEntry::make('created_at')->label(__('Created At'))->inlineLabel(),
                        TextEntry::make('updated_at')->label(__('Last Updated'))->inlineLabel(),
                    ];
                }),

                EditAction::make()
                    ->slideOver()
                    ->form(function ($record) {
                        return [
                            TextInput::make('name_en')
                                ->label(__('In English'))
                                ->columnSpanFull()
                                ->required()
                                ->unique('tags', 'name_en', ignorable: $record)
                                ->maxLength(255),

                            TextInput::make('name_ar')
                                ->label(__('In Arabic'))
                                ->columnSpanFull()
                                ->required()
                                ->unique('tags', 'name_ar', ignorable: $record)
                                ->maxLength(255),
                        ];
                    }),

                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public function getSubmitAction(): array
    {
        return [
            Action::make('create')
                ->color('primary')
                ->label(__('Save'))
                ->submit('create'),
        ];
    }
}
