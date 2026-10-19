<x-layouts::app :title="'Ticket #'.$ticket->id">
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a href="{{ route('tickets.index') }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">← Volver a tickets</a>
                <h1 class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">#{{ $ticket->id }} · {{ $ticket->subject }}</h1>
                <p class="text-sm text-zinc-500">Creado por {{ $ticket->user->name }} el {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">{{ $ticket->status->label() }}</span>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('status') }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <section class="space-y-5 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 lg:col-span-2">
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Descripción</h2>
                    <p class="mt-2 whitespace-pre-line text-zinc-800 dark:text-zinc-200">{{ $ticket->description }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Categoría</p>
                        <p class="mt-1 text-zinc-800 dark:text-zinc-200">{{ $ticket->category }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">AnyDesk</p>
                        <p class="mt-1 text-zinc-800 dark:text-zinc-200">
                            {{ $ticket->requires_anydesk ? 'Sí · '.$ticket->anydesk_address : 'No requerido' }}
                        </p>
                    </div>
                </div>

                @if ($ticket->attachments->isNotEmpty())
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Archivos adjuntos</h2>
                        <ul class="mt-2 space-y-2">
                            @foreach ($ticket->attachments as $attachment)
                                <li><a class="text-sm text-indigo-600 hover:underline dark:text-indigo-400" href="{{ route('attachments.download', $attachment) }}">{{ $attachment->original_name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </section>

            <aside class="space-y-4">
                @if (auth()->user()->canManageTickets())
                    <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="space-y-4 rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                        @csrf
                        @method('PUT')
                        <h2 class="font-semibold text-zinc-900 dark:text-white">Actualizar estado</h2>
                        <select name="status" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected($ticket->status === $status)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <button class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Guardar estado</button>
                    </form>
                @endif
            </aside>
        </div>

        <section class="space-y-4 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Comentarios</h2>
            <div class="space-y-3">
                @forelse ($ticket->comments as $comment)
                    <article class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800">
                        <div class="flex justify-between gap-4 text-sm">
                            <strong class="text-zinc-800 dark:text-zinc-200">{{ $comment->user->name }}</strong>
                            <span class="text-zinc-500">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="mt-2 whitespace-pre-line text-sm text-zinc-700 dark:text-zinc-300">{{ $comment->body }}</p>
                    </article>
                @empty
                    <p class="text-sm text-zinc-500">Todavía no hay comentarios.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" class="space-y-3">
                @csrf
                <label for="body" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">Agregar comentario</label>
                <textarea id="body" name="body" rows="3" required maxlength="3000" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">{{ old('body') }}</textarea>
                @error('body') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                <button class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900">Publicar comentario</button>
            </form>
        </section>
    </div>
</x-layouts::app>
