<x-layouts::app title="Inventario de equipos">
    <div class="space-y-6">
        <header class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div><h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Inventario de equipos</h1><p class="text-sm text-zinc-500">Control unificado de celulares, equipos y monitores.</p></div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('equipment.export.excel', request()->query()) }}" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-semibold hover:bg-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-800">Exportar Excel</a>
                <a href="{{ route('equipment.export.pdf', request()->query()) }}" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-semibold hover:bg-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-800">Exportar PDF</a>
                <a href="{{ route('equipment.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Registrar</a>
            </div>
        </header>

        @if (session('status')) <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('status') }}</div> @endif

        <section class="rounded-xl border border-pineapple-orange/40 bg-pineapple-cream/70 p-5 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div><h2 class="font-semibold text-zinc-900 dark:text-white">Cargar desde Excel</h2><p class="mt-1 text-sm text-zinc-500">Compatible con el formato unificado y sus bloques de celulares, equipos y monitores.</p><a href="{{ route('equipment.template') }}" class="mt-2 inline-block text-sm font-semibold text-indigo-600 hover:underline dark:text-indigo-400">Descargar plantilla</a></div>
                <form method="POST" action="{{ route('equipment.import') }}" enctype="multipart/form-data" class="flex flex-col gap-2 sm:flex-row sm:items-end" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').textContent='Importando…';">
                    @csrf
                    <div><label for="inventory_file" class="mb-1 block text-sm font-medium">Archivo Excel</label><input id="inventory_file" name="inventory_file" type="file" accept=".xlsx,.xls,.csv" required class="block max-w-full text-sm file:me-3 file:rounded-lg file:border-0 file:bg-zinc-200 file:px-4 file:py-2 file:font-semibold dark:file:bg-zinc-700"></div>
                    <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Importar</button>
                </form>
            </div>
            @error('inventory_file') <p class="mt-3 text-sm font-medium text-red-600">{{ $message }}</p> @enderror
        </section>

        <form id="inventory-filters" method="GET" action="{{ route('equipment.index') }}#inventory-results" class="grid gap-4 rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900 lg:grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))_auto] lg:items-end">
            <div><label for="equipment-search" class="mb-2 block text-sm font-medium">Buscar</label><input id="equipment-search" type="search" name="q" value="{{ request('q') }}" maxlength="100" placeholder="Nombre o marca" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800"></div>
            <div><label for="equipment-category" class="mb-2 block text-sm font-medium">Categoría</label><select id="equipment-category" name="category" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800"><option value="">Todas</option>@foreach ($categories as $category)<option value="{{ $category->value }}" @selected(request('category') === $category->value)>{{ $category->label() }}</option>@endforeach</select></div>
            <div><label for="equipment-status" class="mb-2 block text-sm font-medium">Estado</label><select id="equipment-status" name="status" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800"><option value="">Todos</option><option value="active" @selected(request('status') === 'active')>Activos</option><option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option></select></div>
            <div><label for="equipment-department" class="mb-2 block text-sm font-medium">Departamento</label><select id="equipment-department" name="department" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800"><option value="">Todos</option>@foreach ($departments as $department)<option value="{{ $department }}" @selected(request('department') === $department)>{{ $department }}</option>@endforeach</select></div>
            <div class="flex gap-2"><button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Filtrar</button>@if ($hasFilters)<a href="{{ route('equipment.index') }}#inventory-results" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-semibold">Limpiar</a>@endif</div>
        </form>

        <section id="inventory-results" class="scroll-mt-6 space-y-3">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-zinc-500">
                    @if ($equipment->total())
                        Mostrando {{ $equipment->firstItem() }}–{{ $equipment->lastItem() }} de {{ $equipment->total() }} registros
                    @else
                        No hay registros para mostrar
                    @endif
                </p>
                <div class="flex items-center gap-2">
                    @if ($equipment->onFirstPage())
                        <span class="cursor-not-allowed rounded-lg border border-zinc-200 px-3 py-2 text-sm text-zinc-400 dark:border-zinc-800">← Anterior</span>
                    @else
                        <a href="{{ $equipment->previousPageUrl() }}" class="rounded-lg border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-800">← Anterior</a>
                    @endif
                    <span class="text-sm font-medium">Página {{ $equipment->currentPage() }} de {{ max(1, $equipment->lastPage()) }}</span>
                    @if ($equipment->hasMorePages())
                        <a href="{{ $equipment->nextPageUrl() }}" class="rounded-lg border border-zinc-300 px-3 py-2 text-sm font-semibold hover:bg-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-800">Siguiente →</a>
                    @else
                        <span class="cursor-not-allowed rounded-lg border border-zinc-200 px-3 py-2 text-sm text-zinc-400 dark:border-zinc-800">Siguiente →</span>
                    @endif
                </div>
            </div>

        <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900"><div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                <thead class="bg-zinc-50 text-left text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-800"><tr><th class="px-5 py-3">Tipo</th><th class="px-5 py-3">Movimiento</th><th class="px-5 py-3">Fecha</th><th class="px-5 py-3">Colaborador</th><th class="px-5 py-3">Área o puesto</th><th class="px-5 py-3">Sociedad</th><th class="px-5 py-3">Línea / Número</th><th class="px-5 py-3">Estado</th><th class="px-5 py-3 text-right">Acción</th></tr></thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($equipment as $item)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60"><td class="px-5 py-4 font-semibold">{{ $item->category->importLabel() }}</td><td class="px-5 py-4">{{ $item->movement ?: '—' }}</td><td class="px-5 py-4 whitespace-nowrap">{{ $item->movement_date ?: '—' }}</td><td class="px-5 py-4 font-semibold text-zinc-900 dark:text-white">{{ $item->collaborator_name }}</td><td class="px-5 py-4">{{ $item->department }}</td><td class="px-5 py-4">{{ $item->society ?: '—' }}</td><td class="px-5 py-4 whitespace-nowrap">{{ $item->has_business_line === null ? '—' : ($item->has_business_line ? 'Sí' : 'No') }}@if($item->phone_number)<span class="block text-xs text-zinc-500">{{ $item->phone_number }}</span>@endif</td><td class="px-5 py-4"><span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $item->is_active ? 'border-emerald-300 bg-emerald-100 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'border-zinc-300 bg-zinc-100 text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300' }}">{{ $item->is_active ? 'Activo' : 'Inactivo' }}</span></td><td class="px-5 py-4 text-right"><a href="{{ route('equipment.edit', ['equipment' => $item, 'return_to' => url()->full().'#inventory-results']) }}" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">Editar</a></td></tr>
                    @empty <tr><td colspan="9" class="px-5 py-12 text-center text-zinc-500">{{ $hasFilters ? 'No hay registros que coincidan con los filtros.' : 'Todavía no hay equipos registrados.' }}</td></tr> @endforelse
                </tbody>
            </table>
        </div></div>
        {{ $equipment->links() }}
        </section>
    </div>
</x-layouts::app>
