<x-layouts::app title="Reportes de tickets">
    <div class="mx-auto max-w-5xl space-y-6">
        <header>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">Mesa de ayuda</p>
            <h1 class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">Reporte mensual de tickets</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Genere un PDF general o consulte únicamente los tickets creados por un colaborador.</p>
        </header>

        <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 bg-zinc-50 px-6 py-5 dark:border-zinc-700 dark:bg-zinc-800">
                <h2 class="font-semibold text-zinc-900 dark:text-white">Configurar reporte</h2>
                <p class="mt-1 text-sm text-zinc-500">El período incluye todos los tickets creados desde el primer hasta el último día del mes.</p>
            </div>

            <form method="GET" action="{{ route('reports.tickets.pdf') }}" class="grid gap-6 p-6 md:grid-cols-2" target="_blank">
                <div>
                    <label for="report-month" class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">Mes del reporte</label>
                    <input id="report-month" name="month" type="month" value="{{ old('month', $month) }}" required class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    @error('month') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="report-user" class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">Colaborador</label>
                    <select id="report-user" name="user_id" class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        <option value="">Todos los colaboradores (reporte general)</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((string) $selectedUserId === (string) $user->id)>{{ $user->name }} · {{ $user->email }}</option>
                        @endforeach
                    </select>
                    @error('user_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 flex flex-col gap-3 rounded-xl border border-pineapple-orange/30 bg-pineapple-cream/70 p-4 sm:flex-row sm:items-center sm:justify-between dark:bg-zinc-800/70">
                    <div>
                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">El PDF se descargará automáticamente</p>
                        <p class="mt-1 text-xs text-zinc-500">Incluye totales por estado, solicitante, categoría, fecha y detalle de cada ticket.</p>
                    </div>
                    <button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-500">
                        Generar reporte PDF
                    </button>
                </div>
            </form>
        </section>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900"><p class="text-sm font-semibold">Reporte general</p><p class="mt-1 text-sm text-zinc-500">Deje el colaborador en “Todos” para incluir toda la organización.</p></div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900"><p class="text-sm font-semibold">Reporte individual</p><p class="mt-1 text-sm text-zinc-500">Seleccione una persona para consultar solamente sus solicitudes.</p></div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900"><p class="text-sm font-semibold">Período mensual</p><p class="mt-1 text-sm text-zinc-500">Puede consultar cualquier mes que tenga tickets registrados.</p></div>
        </div>
    </div>
</x-layouts::app>
