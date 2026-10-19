<x-layouts::app title="Crear ticket">
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <a href="{{ route('tickets.index') }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">← Volver a tickets</a>
            <h1 class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">Crear un nuevo ticket</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Describa el problema para que el equipo de TI pueda atenderlo.</p>
        </div>

        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-6 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            @csrf

            <div>
                <label for="subject" class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">Asunto</label>
                <input id="subject" name="subject" value="{{ old('subject') }}" required maxlength="150" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="category" class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">Categoría</label>
                <select id="category" name="category" required class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    <option value="">Seleccione una categoría</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
                @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">Descripción del problema</label>
                <textarea id="description" name="description" rows="6" required maxlength="5000" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div x-data="{ requiresAnyDesk: {{ old('requires_anydesk') ? 'true' : 'false' }} }" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                <input type="hidden" name="requires_anydesk" value="0">
                <label class="flex items-center gap-3 text-sm font-medium text-zinc-800 dark:text-zinc-200">
                    <input type="checkbox" name="requires_anydesk" value="1" x-model="requiresAnyDesk" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                    Se requiere AnyDesk
                </label>

                <div x-show="requiresAnyDesk" x-cloak class="mt-4">
                    <label for="anydesk_address" class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">Dirección o código de AnyDesk</label>
                    <input id="anydesk_address" name="anydesk_address" value="{{ old('anydesk_address') }}" maxlength="50" :required="requiresAnyDesk" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-zinc-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    <p class="mt-1 text-xs text-zinc-500">No escriba la contraseña de AnyDesk.</p>
                    @error('anydesk_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="attachment" class="mb-2 block text-sm font-medium text-zinc-800 dark:text-zinc-200">Archivo adjunto (opcional)</label>
                <input id="attachment" name="attachment" type="file" class="block w-full text-sm text-zinc-600 file:me-4 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:font-medium dark:text-zinc-300 dark:file:bg-zinc-800">
                <p class="mt-1 text-xs text-zinc-500">Tamaño máximo: 10 MB.</p>
                @error('attachment') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">Registrar ticket</button>
            </div>
        </form>
    </div>
</x-layouts::app>
