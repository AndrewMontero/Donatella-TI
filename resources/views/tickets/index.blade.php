<x-layouts::app title="Tickets">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Tickets</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ auth()->user()->canManageTickets() ? 'Solicitudes registradas en el sistema.' : 'Seguimiento de sus solicitudes de soporte.' }}
                </p>
            </div>
            <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Crear ticket
            </a>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <form method="GET" action="{{ route('tickets.index') }}" class="grid gap-4 rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">
            <div>
                <label for="ticket-search" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-200">Buscar</label>
                <input id="ticket-search" type="search" name="q" value="{{ request('q') }}" maxlength="100" placeholder="Número, asunto{{ auth()->user()->canManageTickets() ? ', solicitante o correo' : '' }}" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
            </div>

            <div>
                <label for="ticket-status" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-200">Estado</label>
                <select id="ticket-status" name="status" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
                    <option value="">Todos los estados</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="ticket-category" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-200">Categoría</label>
                <select id="ticket-category" name="category" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
                    <option value="">Todas las categorías</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">Filtrar</button>
                @if ($hasFilters)
                    <a href="{{ route('tickets.index') }}" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">Limpiar</a>
                @endif
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                            <th class="px-5 py-3">Ticket</th>
                            @if (auth()->user()->canManageTickets())
                                <th class="px-5 py-3">Solicitante</th>
                            @endif
                            <th class="px-5 py-3">Categoría</th>
                            <th class="px-5 py-3">Estado</th>
                            <th class="px-5 py-3">AnyDesk</th>
                            <th class="px-5 py-3">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($tickets as $ticket)
                            <tr class="text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-800/60">
                                <td class="px-5 py-4">
                                    <a href="{{ route('tickets.show', $ticket) }}" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">
                                        #{{ $ticket->id }} · {{ $ticket->subject }}
                                    </a>
                                </td>
                                @if (auth()->user()->canManageTickets())
                                    <td class="px-5 py-4">{{ $ticket->user->name }}</td>
                                @endif
                                <td class="px-5 py-4">{{ $ticket->category }}</td>
                                <td class="px-5 py-4"><x-ticket-status-badge :status="$ticket->status" /></td>
                                <td class="px-5 py-4">{{ $ticket->requires_anydesk ? 'Sí' : 'No' }}</td>
                                <td class="px-5 py-4">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->canManageTickets() ? 6 : 5 }}" class="px-5 py-10 text-center text-sm text-zinc-500">
                                    {{ $hasFilters ? 'No hay tickets que coincidan con los filtros.' : 'Todavía no hay tickets registrados.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $tickets->links() }}
    </div>
</x-layouts::app>
