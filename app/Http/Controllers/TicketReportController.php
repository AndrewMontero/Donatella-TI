<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class TicketReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('reports.tickets.index', [
            'month' => $request->string('month', now()->format('Y-m'))->toString(),
            'selectedUserId' => $request->integer('user_id') ?: null,
            'users' => $this->usersWithTickets(),
        ]);
    }

    public function pdf(Request $request): Response
    {
        $validated = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ]);

        $start = Carbon::createFromFormat('Y-m-d', $validated['month'].'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $selectedUser = filled($validated['user_id'] ?? null)
            ? User::query()->findOrFail($validated['user_id'])
            : null;

        $query = Ticket::query()
            ->with('user')
            ->whereBetween('created_at', [$start, $end])
            ->oldest('created_at');

        if ($selectedUser) {
            $query->whereBelongsTo($selectedUser);
        }

        $tickets = $query->get();
        $monthLabel = ucfirst($start->locale('es')->translatedFormat('F Y'));

        $pdf = Pdf::loadView('reports.tickets.pdf', [
            'tickets' => $tickets,
            'selectedUser' => $selectedUser,
            'monthLabel' => $monthLabel,
            'generatedAt' => now(),
            'counts' => [
                'pending' => $tickets->where('status', TicketStatus::Pending)->count(),
                'inProgress' => $tickets->where('status', TicketStatus::InProgress)->count(),
                'done' => $tickets->where('status', TicketStatus::Done)->count(),
            ],
        ])->setPaper('a4', 'landscape');

        $scope = $selectedUser ? '-'.str($selectedUser->name)->slug() : '-general';

        return $pdf->download('reporte-tickets-'.$validated['month'].$scope.'.pdf');
    }

    /** @return Collection<int, User> */
    private function usersWithTickets(): Collection
    {
        return User::query()
            ->whereHas('tickets')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }
}
