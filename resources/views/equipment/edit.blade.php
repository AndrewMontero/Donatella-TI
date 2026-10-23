<x-layouts::app :title="'Editar '.$equipment->collaborator_name">
    <div class="mx-auto max-w-4xl space-y-6">
        <header>
            <a href="{{ $returnTo }}" class="text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400">← Volver a los resultados</a>
            <h1 class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">Editar equipo</h1>
            <p class="mt-1 text-sm text-zinc-500">{{ $equipment->collaborator_name }} · {{ $equipment->category->label() }}</p>
        </header>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('equipment.update', $equipment) }}" class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            @include('equipment._form', ['equipment' => $equipment, 'returnTo' => $returnTo])
        </form>
    </div>
</x-layouts::app>
