<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\TicketController;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('dashboard', function (Request $request) {
        $query = Ticket::query();

        if (! $request->user()->canManageTickets()) {
            $query->whereBelongsTo($request->user());
        }

        return view('dashboard', [
            'totalTickets' => (clone $query)->count(),
            'openTickets' => (clone $query)->whereNotIn('status', ['resolved', 'closed'])->count(),
            'closedTickets' => (clone $query)->where('status', 'closed')->count(),
            'recentTickets' => $query->latest()->limit(5)->get(),
        ]);
    })->name('dashboard');

    Route::resource('tickets', TicketController::class)->only(['index', 'create', 'store', 'show', 'update']);
    Route::post('tickets/{ticket}/comments', [TicketController::class, 'storeComment'])->name('tickets.comments.store');
    Route::get('attachments/{attachment}/download', [TicketController::class, 'downloadAttachment'])->name('attachments.download');

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update']);
    });
});

require __DIR__.'/settings.php';
