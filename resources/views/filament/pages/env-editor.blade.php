<x-filament::page>
    <div class="p-4 mb-6 text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-red-950 dark:text-red-200 dark:border-red-800">
        <div class="flex items-center mb-2 font-semibold">

            {{ __('env.warning.title') }}
        </div>
        <p>{{ __('env.warning.body') }}</p>
    </div>

    {{ $this->form }}

    <x-filament::button wire:click="save" class="mt-4" color="primary">
        {{ __('Save') }}
    </x-filament::button>
</x-filament::page>
