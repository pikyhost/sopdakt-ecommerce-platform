<x-filament::page>
    {{ $this->form }}

    <x-filament::button wire:click="save" class="mt-4" color="primary">
        {{ __('Save') }}
    </x-filament::button>
</x-filament::page>
