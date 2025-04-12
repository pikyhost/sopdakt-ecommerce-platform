<?php

namespace App\Filament\Resources;

use App\Models\AboutUs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AboutUsResource extends Resource
{
    protected static ?string $model = AboutUs::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationLabel = 'About Us Page';

    protected static ?string $modelLabel = 'About Us Content';

    protected static ?string $navigationGroup = 'Content Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('AboutUsContent')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Header & Titles')
                            ->schema([
                                Forms\Components\TextInput::make('header_title')
                                    ->label('Header Title')
                                    ->required(),

                                Forms\Components\TextInput::make('about_title')
                                    ->label('About Section Title')
                                    ->required(),

                                Forms\Components\TextInput::make('team_title')
                                    ->label('Team Section Title'),

                                Forms\Components\TextInput::make('testimonial_title')
                                    ->label('Testimonial Section Title'),

                                Forms\Components\TextInput::make('breadcrumb_home')
                                    ->label('Breadcrumb Home Text'),

                                Forms\Components\TextInput::make('breadcrumb_current')
                                    ->label('Breadcrumb Current Page Text'),
                            ]),

                        Forms\Components\Tabs\Tab::make('About Content')
                            ->schema([
                                Forms\Components\FileUpload::make('about_image')
                                    ->label('About Image')
                                    ->image()
                                    ->directory('about-us'),

                                Forms\Components\Textarea::make('about_description_1')
                                    ->label('First Description')
                                    ->rows(3),

                                Forms\Components\Textarea::make('about_description_2')
                                    ->label('Second Description')
                                    ->rows(3),
                            ]),

                        Forms\Components\Tabs\Tab::make('Accordion')
                            ->schema([
                                Forms\Components\Repeater::make('accordion_items')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Title')
                                            ->required(),
                                        Forms\Components\Textarea::make('content')
                                            ->label('Content')
                                            ->rows(3)
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->hidden(),

                                ...collect(range(1, 4))->map(function ($i) {
                                    return Forms\Components\Section::make('Accordion Item '.$i)
                                        ->schema([
                                            Forms\Components\TextInput::make('accordion_title_'.$i)
                                                ->label('Title'),
                                            Forms\Components\Textarea::make('accordion_content_'.$i)
                                                ->label('Content')
                                                ->rows(3),
                                        ])->collapsible();
                                })->toArray()
                            ]),

                        Forms\Components\Tabs\Tab::make('Team Members')
                            ->schema([
                                Forms\Components\Repeater::make('team_members')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Member Name')
                                            ->required(),
                                        Forms\Components\FileUpload::make('image')
                                            ->label('Member Photo')
                                            ->image()
                                            ->directory('team-members')
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                            ]),

                        Forms\Components\Tabs\Tab::make('Testimonial')
                            ->schema([
                                Forms\Components\Textarea::make('testimonial_content')
                                    ->label('Testimonial Text')
                                    ->rows(3),

                                Forms\Components\TextInput::make('testimonial_name')
                                    ->label('Client Name'),

                                Forms\Components\TextInput::make('testimonial_role')
                                    ->label('Client Role/Position'),

                                Forms\Components\FileUpload::make('testimonial_image')
                                    ->label('Client Photo')
                                    ->image()
                                    ->directory('testimonials'),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('header_title')
                    ->label('Header Title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('about_title')
                    ->label('About Title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
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
