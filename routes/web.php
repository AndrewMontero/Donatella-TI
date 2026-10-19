<?php

use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\TicketController;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('dashboard', function (Request $request) {
        if (! $request->user()->canManageTickets()) {
            return redirect()->route('tickets.index');
        }

        $query = Ticket::query();

        return view('dashboard', [
            'totalTickets' => (clone $query)->count(),
            'openTickets' => (clone $query)->whereNot('status', 'done')->count(),
            'doneTickets' => (clone $query)->where('status', 'done')->count(),
            'recentTickets' => $query->latest()->limit(5)->get(),
        ]);
    })->name('dashboard');

    Route::resource('tickets', TicketController::class)->only(['index', 'create', 'store', 'show', 'update']);
    Route::post('tickets/{ticket}/comments', [TicketController::class, 'storeComment'])->name('tickets.comments.store');
    Route::get('attachments/{attachment}/download', [TicketController::class, 'downloadAttachment'])->name('attachments.download');

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('audit-logs', AdminAuditLogController::class)->name('audit-logs.index');
        Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update']);
        Route::resource('categories', AdminCategoryController::class)->only(['index', 'store', 'update']);
    });
});

require __DIR__.'/settings.php';
