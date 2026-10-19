<x-layouts::app title="Categorías">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Categorías de tickets</h1>
            <p class="text-sm text-zinc-500">Administre las opciones que aparecen al crear un ticket.</p>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.categories.store') }}" class="flex flex-col gap-3 rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900 sm:flex-row sm:items-end">
            @csrf
            <div class="flex-1">
                <label for="new-category" class="mb-2 block text-sm font-medium">Nueva categoría</label>
                <input id="new-category" name="name" value="{{ old('name') }}" required maxlength="80" placeholder="Ejemplo: Telefonía" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <button class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">Agregar categoría</button>
        </form>

        <div class="space-y-3">
            @foreach ($categories as $category)
                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="grid gap-4 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900 sm:grid-cols-[1fr_auto_auto] sm:items-center">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="category-{{ $category->id }}" class="sr-only">Nombre de categoría</label>
                        <input id="category-{{ $category->id }}" name="name" value="{{ $category->name }}" required maxlength="80" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
                    </div>
                    <div>
                        <input type="hidden" name="is_active" value="0">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_active" value="1" @checked($category->is_active) class="rounded border-zinc-300 text-indigo-600">
                            Activa
                        </label>
                    </div>
                    <button class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">Guardar</button>
                </form>
            @endforeach
        </div>
    </div>
</x-layouts::app>
