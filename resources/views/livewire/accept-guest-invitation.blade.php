<x-filament::page>
    <div class="flex flex-col items-center">
        <h2 class="text-2xl font-bold">{{ __('Accept Guest Invitation') }}</h2>
        <p class="text-gray-500">{{ __('Complete your registration to join as a guest.') }}</p>
        {{ $this->form }}
    </div>
</x-filament::page>
