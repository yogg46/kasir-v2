<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
    @stack('scripts')
</x-layouts.app.sidebar>