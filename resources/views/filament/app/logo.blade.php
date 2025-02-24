@php
    $settings = \App\Models\Setting::getSetting('site_settings');
    $locale = app()->getLocale();
    $lightLogo = $settings['logo'][$locale] ?? null;
    $darkLogo = $settings['dark_logo'][$locale] ?? null;
@endphp

<div
    x-data="{
        mode: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
        updateMode() {
            this.mode = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        },
        getLogo() {
            return this.mode === 'dark' ? '{{ asset('storage/' . $darkLogo) }}' : '{{ asset('storage/' . $lightLogo) }}';
        }
    }"
    x-init="updateMode()"
    @theme-changed.window="updateMode()"
    class="flex items-center"
>
    <img
        :src="getLogo()"
        alt="Site Logo"
        class="h-10 w-auto"
        x-cloak
    >
</div>
