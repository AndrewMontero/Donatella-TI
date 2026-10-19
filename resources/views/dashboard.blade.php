<x-layouts::app title="Panel de control">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Hola, {{ auth()->user()->name }}</h1>
                <p class="text-sm text-zinc-500">Resumen de la Mesa de Ayuda TI de Grupo Donatella.</p>
            </div>
            <a href="{{ route('tickets.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-indigo-500">Crear ticket</a>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900"><p class="text-sm text-zinc-500">Total de tickets</p><p class="mt-2 text-3xl font-semibold">{{ $totalTickets }}</p></div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900"><p class="text-sm text-zinc-500">Pendientes</p><p class="mt-2 text-3xl font-semibold text-amber-600">{{ $openTickets }}</p></div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900"><p class="text-sm text-zinc-500">Hechos</p><p class="mt-2 text-3xl font-semibold text-emerald-600">{{ $doneTickets }}</p></div>
        </div>

        <section class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Tickets recientes</h2>
                <a href="{{ route('tickets.index') }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">Ver todos</a>
            </div>
            <div class="space-y-3">
                @forelse ($recentTickets as $ticket)
                    <a href="{{ route('tickets.show', $ticket) }}" class="flex items-center justify-between gap-4 rounded-lg border border-zinc-100 px-4 py-3 hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-800">
                        <div><p class="font-medium">#{{ $ticket->id }} · {{ $ticket->subject }}</p><p class="text-sm text-zinc-500">{{ $ticket->category }}</p></div>
                        <x-ticket-status-badge :status="$ticket->status" />
                    </a>
                @empty
                    <p class="py-6 text-center text-sm text-zinc-500">No hay tickets registrados.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts::app>
