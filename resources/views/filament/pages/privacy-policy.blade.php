<x-filament-panels::page>
    <div class="prose max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-2xl font-bold mb-4">{{ __('policy.privacy_policy') }}</h1>
        <div class="markdown">
            {!! Filament\Facades\Filament::renderMarkdown($this->getPolicy()) !!}
        </div>
    </div>
</x-filament-panels::page>
