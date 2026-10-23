<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="donatella-main">
        <div class="mb-4 flex justify-end">
            <x-appearance-toggle />
        </div>

        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
