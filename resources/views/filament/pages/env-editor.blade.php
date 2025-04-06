<x-filament::page>
    <x-filament::callout color="danger" icon="heroicon-o-exclamation-triangle" class="mb-6">
        <strong>{{ __('env.warning.title') }}</strong><br>
        {{ __('env.warning.body') }}
    </x-filament::callout>

    {{ $this->form }}

    <x-filament::button wire:click="save" class="mt-4" color="primary">
        {{ __('Save') }}
    </x-filament::button>
</x-filament::page>
