@csrf
@if (isset($equipment)) @method('PUT') @endif

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="collaborator_name" class="mb-2 block text-sm font-medium">Nombre o colaborador</label>
        <input id="collaborator_name" name="collaborator_name" value="{{ old('collaborator_name', $equipment->collaborator_name ?? '') }}" required maxlength="150" placeholder="Nombre del colaborador" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
        @error('collaborator_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="category" class="mb-2 block text-sm font-medium">Categoría</label>
        <select id="category" name="category" required class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
            @foreach ($categories as $category)
                <option value="{{ $category->value }}" @selected(old('category', isset($equipment) ? $equipment->category->value : '') === $category->value)>{{ $category->label() }}</option>
            @endforeach
        </select>
        @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="movement" class="mb-2 block text-sm font-medium">Movimiento</label>
        <input id="movement" name="movement" value="{{ old('movement', $equipment->movement ?? '') }}" maxlength="50" placeholder="Entrega, traslado o listado actual" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
        @error('movement') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="movement_date" class="mb-2 block text-sm font-medium">Fecha</label>
        <input id="movement_date" name="movement_date" value="{{ old('movement_date', $equipment->movement_date ?? '') }}" maxlength="30" placeholder="Ejemplo: Oct-26" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
        @error('movement_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="brand" class="mb-2 block text-sm font-medium">Marca <span class="text-zinc-500">(opcional)</span></label>
        <input id="brand" name="brand" value="{{ old('brand', $equipment->brand ?? '') }}" maxlength="100" placeholder="Ejemplo: Dell o Samsung" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
        @error('brand') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="department" class="mb-2 block text-sm font-medium">Departamento</label>
        <input id="department" name="department" value="{{ old('department', $equipment->department ?? '') }}" required maxlength="120" placeholder="Ejemplo: Contabilidad" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
        @error('department') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="society" class="mb-2 block text-sm font-medium">Sociedad</label>
        <input id="society" name="society" value="{{ old('society', $equipment->society ?? '') }}" maxlength="120" placeholder="Ejemplo: Dolcesia Fruit" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
        @error('society') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="has_business_line" class="mb-2 block text-sm font-medium">Línea empresarial</label>
        <select id="has_business_line" name="has_business_line" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
            <option value="">No aplica</option>
            <option value="1" @selected((string) old('has_business_line', isset($equipment) && $equipment->has_business_line !== null ? (int) $equipment->has_business_line : '') === '1')>Sí</option>
            <option value="0" @selected((string) old('has_business_line', isset($equipment) && $equipment->has_business_line !== null ? (int) $equipment->has_business_line : '') === '0')>No</option>
        </select>
        @error('has_business_line') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="phone_number" class="mb-2 block text-sm font-medium">Número celular</label>
        <input id="phone_number" name="phone_number" value="{{ old('phone_number', $equipment->phone_number ?? '') }}" maxlength="40" placeholder="Ejemplo: 8888-8888" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
        @error('phone_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="source_file" class="mb-2 block text-sm font-medium">Archivo de origen</label>
        <input id="source_file" name="source_file" value="{{ old('source_file', $equipment->source_file ?? '') }}" maxlength="255" placeholder="Nombre del archivo o fuente" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
        @error('source_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<label class="mt-6 flex items-center gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/70">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $equipment->is_active ?? true)) class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
    <span><span class="block text-sm font-semibold">Activo</span><span class="block text-xs text-zinc-500">Desmarque esta opción si el equipo ya no está en uso.</span></span>
</label>

<div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ $returnTo ?? route('equipment.index').'#inventory-results' }}" class="rounded-lg border border-zinc-300 px-5 py-2.5 text-center text-sm font-semibold hover:bg-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-800">Cancelar</a>
    <button class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">{{ isset($equipment) ? 'Guardar cambios' : 'Registrar' }}</button>
</div>
