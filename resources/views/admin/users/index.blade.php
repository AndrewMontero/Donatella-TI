<x-layouts::app title="Administrar usuarios">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Administración de usuarios</h1>
            <p class="text-sm text-zinc-500">Desde aquí puede asignar el rol Técnico, activar cuentas y cambiar contraseñas.</p>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('status') }}</div>
        @endif

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 text-left text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-800">
                        <tr><th class="px-5 py-3">Usuario</th><th class="px-5 py-3">Rol</th><th class="px-5 py-3">Estado</th><th class="px-5 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($users as $user)
                            <tr class="text-sm text-zinc-700 dark:text-zinc-200">
                                <td class="px-5 py-4"><strong>{{ $user->name }}</strong><br><span class="text-zinc-500">{{ $user->email }}</span></td>
                                <td class="px-5 py-4">{{ $user->role->label() }}</td>
                                <td class="px-5 py-4">{{ $user->is_active ? 'Activo' : 'Desactivado' }}</td>
                                <td class="px-5 py-4 text-right"><a href="{{ route('admin.users.edit', $user) }}" class="font-medium text-indigo-600 hover:underline dark:text-indigo-400">Administrar</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $users->links() }}
    </div>
</x-layouts::app>
