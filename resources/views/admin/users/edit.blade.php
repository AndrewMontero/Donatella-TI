<x-layouts::app title="Editar usuario">
    <div class="mx-auto max-w-2xl space-y-6">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">← Volver a usuarios</a>
            <h1 class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $managedUser->name }}</h1>
            <p class="text-sm text-zinc-500">{{ $managedUser->email }}</p>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="space-y-6 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            @csrf
            @method('PUT')

            @if ($managedUser->isNot(auth()->user()))
                <div>
                    <label for="role" class="mb-2 block text-sm font-medium">Rol</label>
                    <select id="role" name="role" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" @selected(old('role', $managedUser->role->value) === $role->value)>{{ $role->label() }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <input type="hidden" name="is_active" value="0">
                <label class="flex items-center gap-3 text-sm font-medium">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $managedUser->is_active)) class="rounded border-zinc-300 text-indigo-600">
                    Cuenta activa
                </label>
            @else
                <div class="rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:bg-amber-950 dark:text-amber-200">Por seguridad, no puede desactivar su propia cuenta ni cambiar su propio rol.</div>
            @endif

            <div class="border-t border-zinc-200 pt-5 dark:border-zinc-700">
                <h2 class="font-semibold">Cambiar contraseña</h2>
                <p class="mb-4 text-sm text-zinc-500">Deje estos campos vacíos si no desea cambiarla.</p>
                <div class="space-y-4">
                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium">Nueva contraseña</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
                        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium">Confirmar contraseña</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">Guardar cambios</button>
            </div>
        </form>
    </div>
</x-layouts::app>
