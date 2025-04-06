<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Filament\Notifications\Notification;

class EnvEditor extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $view = 'filament.pages.env-editor';
    protected static ?string $navigationLabel = '.env Editor';
    protected static ?string $title = 'Edit .env File';

    protected static ?string $slug = 'edit-server-settings';

    public ?string $envContent = '';

    public function mount(): void
    {
        $this->envContent = File::exists(base_path('.env'))
            ? File::get(base_path('.env'))
            : '';
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Textarea::make('envContent')
                ->label('.env Content')
                ->rows(30)
                ->required(),
        ];
    }

    public function save(): void
    {
        try {
            File::put(base_path('.env'), $this->envContent);

            Notification::make()
                ->title('.env file saved successfully.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Failed to save .env file')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
