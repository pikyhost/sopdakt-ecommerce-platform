<?php

namespace App\Filament\Resources;

use App\Models\AboutUs;
use Filament\Forms;

use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AboutUsResource extends Resource
{
    use Translatable;

    protected static ?string $model = AboutUs::class;

    protected static ?string $slug = 'about-us';

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationLabel = 'about_us.navigation_label';

    protected static ?string $modelLabel = 'about_us.model_label';

    protected static ?string $navigationGroup = 'about_us.navigation_group';

    public static function getNavigationLabel(): string
    {
        return __('about_us.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('about_us.model_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Pages Settings Management');
    }

    public static function getPluralModelLabel(): string
    {
        return __('about_us.model_label');
    }

    public static function getPluralLabel(): ?string
    {
        return __('about_us.model_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('AboutUsContent')
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('about_us.tabs.header'))
                            ->schema([
                                Forms\Components\TextInput::make('header_title')
                                    ->label(__('about_us.fields.header_title'))
                                    ->required(),

                                Forms\Components\TextInput::make('about_title')
                                    ->label(__('about_us.fields.about_title'))
                                    ->required(),

                                Forms\Components\TextInput::make('team_title')
                                    ->label(__('about_us.fields.team_title')),

                                Forms\Components\TextInput::make('testimonial_title')
                                    ->label(__('about_us.fields.testimonial_title')),

                                Forms\Components\TextInput::make('breadcrumb_home')
                                    ->label(__('about_us.fields.breadcrumb_home')),

                                Forms\Components\TextInput::make('breadcrumb_current')
                                    ->label(__('about_us.fields.breadcrumb_current')),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('about_us.tabs.about'))
                            ->schema([
                                Forms\Components\FileUpload::make('about_image')
                                    ->label(__('about_us.fields.about_image'))
                                    ->image()
                                    ->directory('about-us'),

                                Forms\Components\Textarea::make('about_description_1')
                                    ->label(__('about_us.fields.about_description_1'))
                                    ->rows(3),

                                Forms\Components\Textarea::make('about_description_2')
                                    ->label(__('about_us.fields.about_description_2'))
                                    ->rows(3),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('about_us.tabs.accordion'))
                            ->schema([
                                Forms\Components\Repeater::make('accordion_items')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label(__('about_us.fields.accordion_title'))
                                            ->required(),
                                        Forms\Components\Textarea::make('content')
                                            ->label(__('about_us.fields.accordion_content'))
                                            ->rows(3)
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->hidden(),

                                ...collect(range(1, 4))->map(function ($i) {
                                    return Forms\Components\Section::make(__('about_us.accordion_section', ['number' => $i]))
                                        ->schema([
                                            Forms\Components\TextInput::make('accordion_title_'.$i)
                                                ->label(__('about_us.fields.accordion_title')),
                                            Forms\Components\Textarea::make('accordion_content_'.$i)
                                                ->label(__('about_us.fields.accordion_content'))
                                                ->rows(3),
                                        ])->collapsible();
                                })->toArray()
                            ]),

                        Forms\Components\Tabs\Tab::make(__('Team'))
                            ->schema([
                                Forms\Components\Section::make([
                                    Forms\Components\TextInput::make('cta_text')
                                        ->label('CTA Text')
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('cta_url')
                                        ->label('CTA Link')
                                        ->url()
                                        ->maxLength(255),
                                ])
                                    ->columns(2)
                                    ->description('Add the text and link for the Call To Action (CTA) section.'),

                                Forms\Components\Section::make('Team Members (English)')
                                    ->description('Add team members in English. Each member should have a name and an image.')
                                    ->schema([
                                        Forms\Components\Repeater::make('team_members')
                                            ->label('') // Label omitted for clarity
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Name')
                                                    ->required(),

                                                Forms\Components\FileUpload::make('image')
                                                    ->label('Image')
                                                    ->image()
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                                    ]),

                                Forms\Components\Section::make('Team Members (Arabic)')
                                    ->description('Add team members in Arabic. Structure should mirror the English team, but use Arabic names.')
                                    ->schema([
                                        Forms\Components\Repeater::make('team_members_ar')
                                            ->label('') // Label omitted for clarity
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('الاسم')
                                                    ->required(),

                                                Forms\Components\FileUpload::make('image')
                                                    ->label('الصورة')
                                                    ->image()
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('about_us.tabs.testimonial'))
                            ->schema([
                                Forms\Components\Textarea::make('testimonial_content')
                                    ->label(__('about_us.fields.testimonial_content'))
                                    ->rows(3),

                                Forms\Components\TextInput::make('testimonial_name')
                                    ->label(__('about_us.fields.testimonial_name')),

                                Forms\Components\TextInput::make('testimonial_role')
                                    ->label(__('about_us.fields.testimonial_role')),

                                Forms\Components\FileUpload::make('testimonial_image')
                                    ->label('Testimonial Image')
                                    ->image()
                                    ->directory('clients')
                                    ->preserveFilenames()
                                    ->maxFiles(1),
                            ]),
                    ])
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('header_title')
                    ->label(__('about_us.fields.header_title'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('about_title')
                    ->label(__('about_us.fields.about_title'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('about_us.fields.updated_at'))
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\AboutUsResource\Pages\ListAboutUs::route('/'),
            'edit' => \App\Filament\Resources\AboutUsResource\Pages\EditAboutUs::route('/{record}/edit'),
        ];
    }
}
