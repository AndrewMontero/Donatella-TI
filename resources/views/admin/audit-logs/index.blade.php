<x-layouts::app title="Historial administrativo">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Historial administrativo</h1>
            <p class="text-sm text-zinc-500">Registro de cambios importantes realizados en la mesa de ayuda.</p>
        </div>

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Usuario</th>
                            <th class="px-4 py-3">Acción</th>
                            <th class="px-4 py-3">Elemento</th>
                            <th class="px-4 py-3">Cambios</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($logs as $log)
                            @php
                                $actionLabels = [
                                    'user.registered' => 'Usuario registrado',
                                    'user.updated' => 'Usuario actualizado',
                                    'category.created' => 'Categoría creada',
                                    'category.updated' => 'Categoría actualizada',
                                    'ticket.status_updated' => 'Estado de ticket actualizado',
                                ];
                                $subjectLabels = [
                                    \App\Models\User::class => 'Usuario',
                                    \App\Models\Category::class => 'Categoría',
                                    \App\Models\Ticket::class => 'Ticket',
                                ];
                            @endphp
                            <tr class="align-top">
                                <td class="whitespace-nowrap px-4 py-3 text-zinc-600 dark:text-zinc-300">
                                    {{ $log->created_at?->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $log->actor?->name ?? 'Sistema' }}</div>
                                    <div class="text-xs text-zinc-500">{{ $log->actor?->email }}</div>
                                </td>
                                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-200">
                                    {{ $actionLabels[$log->action] ?? $log->action }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-zinc-600 dark:text-zinc-300">
                                    {{ $subjectLabels[$log->subject_type] ?? class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($log->changes)
                                        <code class="block max-w-xl whitespace-pre-wrap break-words text-xs text-zinc-600 dark:text-zinc-300">{{ json_encode($log->changes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</code>
                                    @else
                                        <span class="text-zinc-400">Sin cambios detallados</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-zinc-500">Todavía no hay eventos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $logs->links() }}
    </div>
</x-layouts::app>
