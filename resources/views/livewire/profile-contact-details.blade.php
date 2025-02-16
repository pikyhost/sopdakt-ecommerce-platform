<x-filament-breezy::grid-section md=2 title="{{ __('تفاصيل الاتصال') }}" description="{{ __('قم بتحديث رقم الهاتف والعنوان') }}">
    <x-filament::card>
        <form wire:submit.prevent="submit" class="space-y-6">

            {{ $this->form }}

            <div class="text-right">
                <x-filament::button type="submit">
                    {{ __('حفظ') }} <!-- Arabic for "Save" -->
                </x-filament::button>
            </div>
        </form>
    </x-filament::card>
</x-filament-breezy::grid-section>
