<x-filament-breezy::grid-section md=2 title="{{ __('profile.contact_details') }}" description="{{ __('profile.contact_description') }}">
    <x-filament::card>
        <form wire:submit.prevent="submit" class="space-y-6">

            {{ $this->form }}

            <div class="text-right">
                <x-filament::button type="submit">
                    {{ __('Update') }}
                </x-filament::button>
            </div>
        </form>
    </x-filament::card>
</x-filament-breezy::grid-section>
