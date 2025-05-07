<?php

namespace App\Filament\Resources;

use App\Filament\Exports\CountryExporter;
use App\Filament\Exports\UserExporter;
use App\Mail\OfferEmail;
use App\Models\User;
use Closure;
use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\ManageUserOrders;
use App\Traits\HasCreatedAtFilter;
use App\Traits\HasTimestampSection;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Events\Auth\Registered;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use libphonenumber\PhoneNumberUtil;
use Spatie\Permission\Models\Role;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Illuminate\Validation\Rules\Password;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class UserResource extends Resource
{
    use HasCreatedAtFilter, HasTimestampSection;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('user.label');
    }

    public static function getModelLabel(): string
    {
        return __('user.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings Management');
    }

    public static function getPluralLabel(): ?string
    {
        return __('user.label');
    }

    public static function getLabel(): ?string
    {
        return __('user.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('user.label');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewUser::class,
            Pages\EditUser::class,
            ManageUserOrders::class
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            self::getMainSection(),
            self::getTimestampSection(),
        ])
            ->columns(3);
    }

    private static function getMainSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make()->schema([
            Forms\Components\FileUpload::make('avatar_url')
                ->label(__('Upload avatar image'))
                ->image()
                ->avatar()
                ->imageEditor()
                ->maxSize(10 * 1024 * 1024)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('name')
                ->label(__('name'))
                ->required()
                ->maxLength(255)
                ->dehydrateStateUsing(fn (string $state): string => ucwords($state)),

            TextInput::make('email')
                ->label(__('Email address'))
                ->required()
                ->email()
                ->rules(['email:rfc,dns'])
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->afterStateUpdated(function ($state, $livewire) {
                    if ($livewire->record && $livewire->record->email !== $state) {
                        event(new Registered($livewire->record));
                    }
                }),

            PhoneInput::make('phone')
                ->separateDialCode(true)
                ->enableIpLookup(true)
                ->initialCountry(fn () => geoip(request()->ip())['country_code2'] ?? 'US')
                ->required()
                ->rules([
                    // Dynamic validation based on country code
                    fn ($get) => function ($attribute, $value, $fail) use ($get) {
                        // Get the country code from the countryStatePath
                        $countryCode =  'SA'; // Ensure uppercase and fallback to EG

                        // Define country-specific length rules (total length in E164 format, including +)
                        $lengthRules = [
                            // Gulf countries
                            'AE' => ['min' => 12, 'max' => 12], // UAE: +971501234567
                            'SA' => ['min' => 13, 'max' => 13], // Saudi Arabia: +966512345678
                            'KW' => ['min' => 12, 'max' => 12], // Kuwait: +96512345678
                            'OM' => ['min' => 12, 'max' => 12], // Oman: +96891234567
                            'QA' => ['min' => 11, 'max' => 11], // Qatar: +9741234567
                            'BH' => ['min' => 11, 'max' => 11], // Bahrain: +9731234567

                            // North African countries
                            'EG' => ['min' => 13, 'max' => 13], // Egypt: +201234567890
                            'LY' => ['min' => 12, 'max' => 12], // Libya: +218912345678
                            'MA' => ['min' => 13, 'max' => 13], // Morocco: +212612345678
                            'TN' => ['min' => 12, 'max' => 12], // Tunisia: +21612345678
                            'DZ' => ['min' => 13, 'max' => 13], // Algeria: +213612345678

                            // Western countries
                            'US' => ['min' => 12, 'max' => 12], // USA: +12025550123
                            'GB' => ['min' => 13, 'max' => 13], // UK: +447912345678
                            'CA' => ['min' => 12, 'max' => 12], // Canada: +15195550123
                            'AU' => ['min' => 12, 'max' => 12], // Australia: +61412345678
                            'DE' => ['min' => 13, 'max' => 13], // Germany: +4915123456789
                            'FR' => ['min' => 13, 'max' => 13], // France: +33612345678
                        ];

                        // Use rules for the selected country or fallback to Egypt
                        $rules = $lengthRules[$countryCode] ?? $lengthRules['EG'];

                        // Combine the dial code and phone number to validate the full E164 number
                        $fullNumber = $get('phone_dial_code') . $value; // Assuming dial code is stored in phone_dial_code
                        $length = strlen($fullNumber);

                        if ($length < $rules['min'] || $length > $rules['max']) {
                            $fail(__("The phone number must be :length characters for :country.", [
                                'length' => $rules['min'],
                                'country' => $countryCode,
                            ]));
                        }

                        // Validate phone number format using libphonenumber
                        $phoneUtil = PhoneNumberUtil::getInstance();
                        try {
                            $phoneNumber = $phoneUtil->parse($fullNumber, $countryCode);
                            if (!$phoneUtil->isValidNumber($phoneNumber)) {
                                $fail(__("The phone number is not valid for :country.", ['country' => $countryCode]));
                            }
                        } catch (\Libphonenumber\NumberParseException $e) {
                            $fail(__("The phone number format is invalid."));
                        }
                    },
                ])
                ->unique(table: 'users', column: 'phone', ignoreRecord: true)
                ->unique(table: 'users', column: 'second_phone', ignoreRecord: true)
                ->label(__('Phone')),

            PhoneInput::make('second_phone')
                ->separateDialCode(true)
                ->enableIpLookup(true)
                ->initialCountry(fn () => geoip(request()->ip())['country_code2'] ?? 'US')
                ->required()
                ->rules([
                    // Dynamic validation based on country code
                    fn ($get) => function ($attribute, $value, $fail) use ($get) {
                        // Get the country code from the countryStatePath
                        $countryCode =  'SA'; // Ensure uppercase and fallback to EG

                        // Define country-specific length rules (total length in E164 format, including +)
                        $lengthRules = [
                            // Gulf countries
                            'AE' => ['min' => 12, 'max' => 12], // UAE: +971501234567
                            'SA' => ['min' => 13, 'max' => 13], // Saudi Arabia: +966512345678
                            'KW' => ['min' => 12, 'max' => 12], // Kuwait: +96512345678
                            'OM' => ['min' => 12, 'max' => 12], // Oman: +96891234567
                            'QA' => ['min' => 11, 'max' => 11], // Qatar: +9741234567
                            'BH' => ['min' => 11, 'max' => 11], // Bahrain: +9731234567

                            // North African countries
                            'EG' => ['min' => 13, 'max' => 13], // Egypt: +201234567890
                            'LY' => ['min' => 12, 'max' => 12], // Libya: +218912345678
                            'MA' => ['min' => 13, 'max' => 13], // Morocco: +212612345678
                            'TN' => ['min' => 12, 'max' => 12], // Tunisia: +21612345678
                            'DZ' => ['min' => 13, 'max' => 13], // Algeria: +213612345678

                            // Western countries
                            'US' => ['min' => 12, 'max' => 12], // USA: +12025550123
                            'GB' => ['min' => 13, 'max' => 13], // UK: +447912345678
                            'CA' => ['min' => 12, 'max' => 12], // Canada: +15195550123
                            'AU' => ['min' => 12, 'max' => 12], // Australia: +61412345678
                            'DE' => ['min' => 13, 'max' => 13], // Germany: +4915123456789
                            'FR' => ['min' => 13, 'max' => 13], // France: +33612345678
                        ];

                        // Use rules for the selected country or fallback to Egypt
                        $rules = $lengthRules[$countryCode] ?? $lengthRules['EG'];

                        // Combine the dial code and phone number to validate the full E164 number
                        $fullNumber = $get('phone_dial_code') . $value; // Assuming dial code is stored in phone_dial_code
                        $length = strlen($fullNumber);

                        if ($length < $rules['min'] || $length > $rules['max']) {
                            $fail(__("The phone number must be :length characters for :country.", [
                                'length' => $rules['min'],
                                'country' => $countryCode,
                            ]));
                        }

                        // Validate phone number format using libphonenumber
                        $phoneUtil = PhoneNumberUtil::getInstance();
                        try {
                            $phoneNumber = $phoneUtil->parse($fullNumber, $countryCode);
                            if (!$phoneUtil->isValidNumber($phoneNumber)) {
                                $fail(__("The phone number is not valid for :country.", ['country' => $countryCode]));
                            }
                        } catch (\Libphonenumber\NumberParseException $e) {
                            $fail(__("The phone number format is invalid."));
                        }
                    },
                ])
                ->unique(table: 'users', column: 'phone', ignoreRecord: true)
                ->unique(table: 'users', column: 'second_phone', ignoreRecord: true)
                ->label(__('Second Phone Number')),

            Forms\Components\TextInput::make('password')
                ->label(__('Password'))
                ->helperText(function () {
                    return request()->is('admin/users/create') ? '' : __('Leave blank if you do not wish to change your current password');
                })
                ->password()
                ->rules([
                    Password::defaults()
                ])
                ->revealable()
                ->autocomplete(false)
                ->maxLength(255)
                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create'),

            Forms\Components\TextInput::make('password_confirmation')
                ->label(__('Confirm Password'))
                ->password()
                ->revealable()
                ->autocomplete(false)
                ->maxLength(255)
                ->requiredWith('password')
                ->same('password')
                ->dehydrated(false),

            Forms\Components\Select::make('roles')
                ->searchable()
                ->preload()
                ->columnSpanFull()
                ->multiple()
                ->maxItems(1)
                ->label(__('roles'))
                ->relationship('roles', 'name', fn ($query) =>
                auth()->user()?->hasRole('super_admin')
                    ? $query
                    : $query->where('name', '!=', 'super_admin')
                ),

            Forms\Components\DateTimePicker::make('email_verified_at')
                ->columnSpanFull()
                ->default(now())
                ->label(__('Verified at'))
        ])
            ->columns(2)
            ->columnSpan([
                'lg' => fn (?User $record) => $record === null ? 3 : 2,
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->formats([
                        ExportFormat::Xlsx,
                    ])
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->exporter(UserExporter::class),
                Action::make('back')
                    ->label(__('Back to previous page'))
                    ->icon(app()->getLocale() == 'en' ? 'heroicon-m-arrow-right' : 'heroicon-m-arrow-left')
                    ->iconPosition(IconPosition::After)
                    ->color('gray')
                    ->url(url()->previous())
                    ->hidden(fn () => url()->previous() === url()->current()),
            ])
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label(__('Avatar'))
                    ->defaultImageUrl(function ($record) {
                        $nameParts = explode(' ', trim($record->name));
                        $initials = count($nameParts) === 1
                            ? mb_strtoupper(mb_substr($nameParts[0], 0, 1))
                            : mb_strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1));
                        return 'https://ui-avatars.com/api/?background=000000&color=fff&name=' . urlencode($initials);
                    })
                    ->circular()
                    ->grow(false),

                TextColumn::make('id')
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn($state) => '#'.$state)
                    ->label(__('ID'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('orders_count')
                    ->badge()
                    ->color('success')
                    ->label(__('Orders Count'))
                    ->counts('orders')
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('name'))
                    ->tooltip(fn (TextColumn $column): ?string => static::getTooltip($column))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('Email address'))
                    ->tooltip(fn (TextColumn $column): ?string => static::getTooltip($column))
                    ->searchable()
                    ->iconColor('primary')
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('phone')
                    ->iconColor('primary')
                    ->icon('heroicon-o-phone')
                    ->label(__('Phone'))
                    ->placeholder(__('No phone number saved'))
                    ->searchable(),

                TextColumn::make('second_phone')
                    ->iconColor('primary')
                    ->icon('heroicon-o-phone')
                    ->label(__('Second Phone'))
                    ->placeholder(__('No phone number saved'))
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->label(__('roles'))
                    ->placeholder(__('No role now'))
                    ->formatStateUsing(fn (string $state) => UserRole::getLabelFor($state))
                    ->badge()
                    ->color(fn (string $state) => UserRole::getColorFor($state)),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label(__("Active")),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->placeholder(__('No verified yet'))
                    ->label(__("Verified")),

                TextColumn::make('created_at')
                    ->label(__('created_at'))
                    ->toggleable(true, true)
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label(__('User roles'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn (Role $record) => Str::headline($record->name))
                    ->relationship('roles', 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('name', '!=', 'panel_user'))
                    ->columnSpan(['sm' => 2, 'md' => 2, 'lg' => 2, 'xl' => 2, '2xl' => 2, 'default' => 2]),
                DateFilter::make('created_at')
                    ->columnSpan(['sm' => 2, 'md' => 2, 'lg' => 2, 'xl' => 2, '2xl' => 2, 'default' => 2])
                    ->label(__('Creation date')),
                Filter::make('is_active')->toggle()->label(__("Is Active?"))->columnSpanFull(),
            ], Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(4)
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label(__('View')),
                    Tables\Actions\EditAction::make()->color('primary')->label(__('Edit')),
                    DeleteAction::make()
                        ->label(__('Delete'))
                        ->hidden(fn($record) => $record->hasRole('super_admin') &&
                            auth()->user()->hasRole('admin')),

                    Action::make('active')
                        ->hidden(fn($record) => $record->is_active) // Use enum comparison
                        ->label(__('Activate'))
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn ($record) => self::activeUser($record)),

                    Action::make('block')
                        ->hidden(fn($record) =>
                            !$record->is_active ||
                            ($record->hasRole('super_admin') && auth()->user()->hasRole('admin'))
                        )
                        ->label(__('Block'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($record) => self::blockUser($record)),
                ])
                    ->label(__('Actions'))
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalHeading(__('Delete Selected Users'))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $currentUser = auth()->user();

                            // Separate super_admin users
                            $superAdminUsers = $records->filter(fn ($record) => $record->hasRole('super_admin'));
                            $normalUsers = $records->reject(fn ($record) => $record->hasRole('super_admin'));

                            // Prevent admin from deleting super_admin users
                            if ($currentUser->hasRole('admin') && $superAdminUsers->isNotEmpty()) {
                                Notification::make()
                                    ->title(__('Action Denied'))
                                    ->danger()
                                    ->body(__('You cannot delete users with the Super Admin role.'))
                                    ->send();
                            }

                            // Proceed with deletion only for normal users
                            if ($normalUsers->isNotEmpty()) {
                                $deletedCount = $normalUsers->count();
                                $normalUsers->each->delete();

                                $locale = app()->getLocale();

                                $message = $locale === 'ar'
                                    ? "تم حذف {$deletedCount} مستخدم" . ($deletedCount > 1 ? 'ين' : '')
                                    : "{$deletedCount} user" . ($deletedCount > 1 ? 's' : '') . " have been deleted.";

                                Notification::make()
                                    ->title($locale === 'ar' ? 'تم الحذف' : 'Users Deleted')
                                    ->success()
                                    ->body($message)
                                    ->send();
                            }
                        }),

        BulkAction::make('block')
            ->label(__('Block Selected'))
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records) {
                $currentUser = auth()->user();

                // Separate super_admin users
                $superAdminUsers = $records->filter(fn ($record) => $record->hasRole('super_admin'));
                $normalUsers = $records->reject(fn ($record) => $record->hasRole('super_admin'));

                // Prevent admin from blocking super_admin users
                if ($currentUser->hasRole('admin') && $superAdminUsers->isNotEmpty()) {
                    Notification::make()
                        ->title(__('Action Denied'))
                        ->danger()
                        ->body(__('You cannot block users with the Super Admin role.'))
                        ->send();
                }

                // Proceed with blocking only for normal users
                if ($normalUsers->isNotEmpty()) {
                    $normalUsers->each(fn ($record) => self::blockUser($record));

                    Notification::make()
                        ->title(__('Users Blocked'))
                        ->success()
                        ->body(__(':count users have been blocked.', ['count' => $normalUsers->count()]))
                        ->send();
                }
            }),
         \Filament\Tables\Actions\BulkAction::make('active')
             ->label(__('Activate Selected'))
             ->icon('heroicon-o-check-circle')
             ->color('success')
             ->requiresConfirmation()
             ->action(function ($records) {
                            $records->each(fn ($record) => self::activeUser($record));
                        }),
                    Tables\Actions\BulkAction::make('sendOfferOrMessage')
                        ->label(__('Send Offer or Update'))
                        ->icon('heroicon-o-paper-airplane')
                        ->form([
                            Select::make('type')
                                ->label(__('Choose what to send'))
                                ->options([
                                    'discount' => __('Promotional Discount'),
                                    'product'  => __('Product Highlight'),
                                    'article'  => __('Blog Article'),
                                    'custom'   => __('Custom Message'),
                                ])
                                ->required()
                                ->live(),

                            Select::make('discount_id')
                                ->label(__('Select a Discount'))
                                ->options(fn () => \App\Models\Discount::active()->pluck('name', 'id'))
                                ->visible(fn (Get $get) => $get('type') === 'discount')
                                ->required(fn (Get $get) => $get('type') === 'discount'),

                            Select::make('product_id')
                                ->label(__('Select a Product'))
                                ->options(fn () => \App\Models\Product::pluck('name', 'id'))
                                ->visible(fn (Get $get) => $get('type') === 'product')
                                ->required(fn (Get $get) => $get('type') === 'product'),

                            Select::make('blog_id')
                                ->label(__('Select a Blog Article'))
                                ->options(fn () => \App\Models\Blog::latest()->pluck('title', 'id'))
                                ->visible(fn (Get $get) => $get('type') === 'article')
                                ->required(fn (Get $get) => $get('type') === 'article'),

                            RichEditor::make('custom_message')
                                ->label(__('Write your message'))
                                ->visible(fn (Get $get) => $get('type') === 'custom')
                                ->required(fn (Get $get) => $get('type') === 'custom')
                                ->fileAttachmentsDirectory('emails')
                        ])
                        ->action(fn (Collection $records, array $data) => static::sendOffer($records, $data))
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle(__('Your message was successfully sent!'))
                ])
            ])
            ->recordUrl(fn () => '')
            ->defaultSort('orders_count', 'desc');
    }

    private static function activeUser(User $record)
    {
        $record->update(['is_active' => true]);
    }

    private static function blockUser(User $record)
    {
        $record->update(['is_active' => false]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()->schema([
                \Filament\Infolists\Components\Split::make([
                    Grid::make(2)->schema([
                        Group::make([
                            TextEntry::make('id')
                                ->badge()
                                ->color('info')
                                ->label(__('ID')),
                            TextEntry::make('name')->label(__('name')),


                            PhoneEntry::make('phone')
                                ->placeholder(__('No phone number saved'))
                                ->label(__('Phone number'))
                                ->countryColumn('phone_country'),

                            PhoneEntry::make('second_phone')
                                ->placeholder(__('No phone number saved'))
                                ->label(__('Second Phone Number'))
                                ->countryColumn('phone_country'),

                            TextEntry::make('created_at')
                                ->label(__('Creation date'))
                                ->dateTime('D, M j, Y \a\t g:i A'),
                        ]),
                        Group::make([
                            TextEntry::make('roles.name')
                                ->label(__('roles'))
                                ->placeholder(__('No role now'))
                                ->formatStateUsing(fn (string $state) => UserRole::getLabelFor($state))
                                ->badge()
                                ->color(fn (string $state) => UserRole::getColorFor($state)),

                            IconEntry::make('id')
                                ->label(__('Is active'))
                                ->boolean(),

                            TextEntry::make('email')
                                ->label(__('Email address')),
                            TextEntry::make('updated_at')
                                ->label(__('Last modified at'))
                                ->dateTime('D, M j, Y \a\t g:i A'),
                            TextEntry::make('email_verified_at')
                                ->label(__('Verified at'))
                                ->dateTime('D, M j, Y \a\t g:i A'),
                        ]),
                    ]),
                    ImageEntry::make('avatar_url')
                        ->hiddenLabel()
                        ->grow(false),
                ])->from('xl'),
            ]),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['id', 'name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'ID' => $record->id,
            'Name' => $record->name,
            'Email' => $record->email,
        ]);
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return UserResource::getUrl('view', ['record' => $record]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
            'orders' => ManageUserOrders::route('/{record}/orders'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->withCount('orders');

        // Only super_admins can see super_admins
        if (!auth()->user()->hasRole('super_admin')) {
            $query->whereDoesntHave('roles', function($q) {
                $q->where('name', 'super_admin');
            });
        }

        return $query;
    }

    protected static function getTooltip(TextColumn $column): ?string
    {
        return strlen($column->getState()) > $column->getCharacterLimit() ? $column->getState() : null;
    }

    public static function sendOffer(Collection $users, array $data): void
    {
        try {
            foreach ($users as $user) {
                Mail::to($user->email)->sendNow(new OfferEmail($user, $data));
            }
        } catch (\Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());
        }

        Notification::make()
            ->title(__('Your message was successfully sent!'))
            ->success()
            ->send();
    }
}
