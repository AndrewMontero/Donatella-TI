<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Contracts\View\View;

class AuditLogController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.audit-logs.index', [
            'logs' => AuditLog::query()
                ->with('actor')
                ->latest()
                ->paginate(25),
        ]);
    }
}
