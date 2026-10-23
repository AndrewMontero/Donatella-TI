<x-layouts::app title="Registrar equipo">
    <div class="mx-auto max-w-4xl space-y-6">
        <header>
            <a href="{{ route('equipment.index') }}" class="text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400">← Volver al inventario</a>
            <h1 class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">Registrar equipo</h1>
            <p class="mt-1 text-sm text-zinc-500">Agregue un celular, equipo o monitor al inventario de TI.</p>
        </header>

        <form method="POST" action="{{ route('equipment.store') }}" class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            @include('equipment._form')
        </form>
    </div>
</x-layouts::app>
