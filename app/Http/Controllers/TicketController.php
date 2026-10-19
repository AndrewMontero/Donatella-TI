<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Ticket::query()->with('user')->latest();

        if (! $request->user()->canManageTickets()) {
            $query->whereBelongsTo($request->user());
        }

        return view('tickets.index', [
            'tickets' => $query->paginate(12),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tickets.create', [
            'categories' => $this->categories(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'category' => ['required', Rule::in($this->categories())],
            'description' => ['required', 'string', 'max:5000'],
            'requires_anydesk' => ['nullable', 'boolean'],
            'anydesk_address' => ['nullable', 'required_if:requires_anydesk,1', 'string', 'max:50'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $validated['requires_anydesk'] = $request->boolean('requires_anydesk');
        $validated['anydesk_address'] = $validated['requires_anydesk']
            ? $validated['anydesk_address']
            : null;

        unset($validated['attachment']);

        $ticket = new Ticket($validated);
        $ticket->user()->associate($request->user());
        $ticket->save();

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store("tickets/{$ticket->id}", 'local');

            $ticket->attachments()->create([
                'user_id' => $request->user()->id,
                'disk' => 'local',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('status', 'Ticket creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Ticket $ticket): View
    {
        $this->authorizeTicket($request, $ticket);

        return view('tickets.show', [
            'ticket' => $ticket->load(['user', 'comments.user', 'attachments']),
            'statuses' => TicketStatus::cases(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket): never
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($request->user()->canManageTickets(), 403);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(TicketStatus::class)],
        ]);

        $ticket->update($validated);

        return back()->with('status', 'Estado actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket): never
    {
        abort(404);
    }

    public function storeComment(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorizeTicket($request, $ticket);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:3000'],
        ]);

        $ticket->comments()->create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return back()->with('status', 'Comentario agregado.');
    }

    public function downloadAttachment(Request $request, TicketAttachment $attachment): StreamedResponse
    {
        $ticket = $attachment->ticket()->firstOrFail();

        $this->authorizeTicket($request, $ticket);

        abort_unless(Storage::disk($attachment->disk)->exists($attachment->path), 404);

        return Storage::disk($attachment->disk)->download($attachment->path, $attachment->original_name);
    }

    private function authorizeTicket(Request $request, Ticket $ticket): void
    {
        abort_unless(
            $request->user()->canManageTickets() || $ticket->user_id === $request->user()->id,
            403,
        );
    }

    /** @return list<string> */
    private function categories(): array
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['name'])
            ->map(fn (Category $category): string => $category->name)
            ->all();

        return array_values($categories);
    }
}
