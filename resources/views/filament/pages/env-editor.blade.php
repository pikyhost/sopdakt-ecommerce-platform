<x-filament::page>
    {{ $this->form }}

    <x-filament::button wire:click="save" class="mt-4" color="primary">
        Save Changes
    </x-filament::button>
</x-filament::page>
