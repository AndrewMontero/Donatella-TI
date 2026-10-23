<x-layouts::app :title="'Ticket #'.$ticket->id">
    <div class="ticket-page mx-auto max-w-6xl space-y-6">
        <header class="ticket-hero rounded-2xl border p-5 sm:p-6">
            <a href="{{ route('tickets.index') }}" class="ticket-page-link text-sm font-medium hover:underline">← Volver a tickets</a>
            <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-pineapple-bark dark:text-pineapple-apricot">Ticket #{{ $ticket->id }}</p>
                    <h1 class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white sm:text-3xl">{{ $ticket->subject }}</h1>
                    <p class="mt-2 text-sm text-zinc-500">Creado por {{ $ticket->user->name }} · {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <x-ticket-status-badge :status="$ticket->status" class="px-3 py-1.5 text-sm" />
            </div>
        </header>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('status') }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(18rem,1fr)]">
            <section class="ticket-panel rounded-2xl border p-5 sm:p-6">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Información del ticket</h2>

                <div class="ticket-panel-soft mt-5 rounded-xl border p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Descripción</p>
                    <p class="mt-2 whitespace-pre-line text-zinc-800 dark:text-zinc-200">{{ $ticket->description }}</p>
                </div>

                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="ticket-panel-soft rounded-xl border p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Categoría</dt>
                        <dd class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $ticket->category }}</dd>
                    </div>
                    <div class="ticket-panel-soft rounded-xl border p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Soporte AnyDesk</dt>
                        <dd class="mt-1 font-medium text-zinc-900 dark:text-white">
                            {{ $ticket->requires_anydesk ? 'Requerido · '.$ticket->anydesk_address : 'No requerido' }}
                        </dd>
                    </div>
                </dl>

                @if ($ticket->attachments->isNotEmpty())
                    <div class="mt-6 border-t border-zinc-200 pt-5 dark:border-zinc-700">
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Archivos adjuntos</h3>
                        <ul class="mt-3 space-y-2">
                            @foreach ($ticket->attachments as $attachment)
                                <li>
                                    <a class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400" href="{{ route('attachments.download', $attachment) }}">
                                        Descargar {{ $attachment->original_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </section>

            <aside>
                <div class="ticket-panel rounded-2xl border p-5">
                    <h2 class="font-semibold text-zinc-900 dark:text-white">Estado del ticket</h2>
                    <x-ticket-status-badge :status="$ticket->status" class="mt-3 px-3 py-1.5 text-sm" />

                    @if (auth()->user()->canManageTickets())
                        <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="mt-5 space-y-4 border-t border-zinc-200 pt-5 dark:border-zinc-700">
                            @csrf
                            @method('PUT')
                            <div>
                                <label for="status" class="mb-2 block text-sm font-medium">Cambiar estado</label>
                                <select id="status" name="status" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->value }}" @selected($ticket->status === $status)>{{ $status->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="ticket-page-action w-full rounded-lg px-4 py-2.5 text-sm font-semibold transition-colors">Guardar estado</button>
                        </form>
                    @else
                        <p class="mt-4 text-sm text-zinc-500">El estado es actualizado por el equipo de soporte TI.</p>
                    @endif
                </div>
            </aside>
        </div>

        <section class="ticket-panel rounded-2xl border p-5 sm:p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Comentarios</h2>
                    <p class="text-sm text-zinc-500">Seguimiento y comunicación sobre este ticket.</p>
                </div>
                <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $ticket->comments->count() }}</span>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($ticket->comments as $comment)
                    <article class="ticket-panel-soft rounded-xl border p-4">
                        <div class="flex flex-col gap-1 text-sm sm:flex-row sm:items-center sm:justify-between">
                            <strong class="text-zinc-800 dark:text-zinc-200">{{ $comment->user->name }}</strong>
                            <span class="text-xs text-zinc-500">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="mt-2 whitespace-pre-line text-sm text-zinc-700 dark:text-zinc-300">{{ $comment->body }}</p>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-zinc-300 px-4 py-6 text-center text-sm text-zinc-500 dark:border-zinc-700">Todavía no hay comentarios.</div>
                @endforelse
            </div>

            <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" class="mt-6 space-y-3 border-t border-zinc-200 pt-5 dark:border-zinc-700">
                @csrf
                <label for="body" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">Agregar comentario</label>
                <textarea id="body" name="body" rows="3" required maxlength="3000" placeholder="Escriba una actualización o respuesta…" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">{{ old('body') }}</textarea>
                @error('body') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                <button class="ticket-page-action rounded-lg px-4 py-2.5 text-sm font-semibold transition-colors">Publicar comentario</button>
            </form>
        </section>
    </div>
</x-layouts::app>
