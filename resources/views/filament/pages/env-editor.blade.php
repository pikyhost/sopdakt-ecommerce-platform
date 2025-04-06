<x-filament-panels::page>
    {{ $this->form }}

    <x-filament::button
        wire:click="save"
        color="primary"
        class="mt-4"
    >
        Save .env
    </x-filament::button>
</x-filament-panels::page>
