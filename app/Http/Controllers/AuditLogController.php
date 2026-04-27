<?php

namespace App\Http\Controllers;

use App\Exports\AuditLogsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use OwenIt\Auditing\Models\Audit;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $query = Audit::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', 'like', '%' . $request->auditable_type . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs  = $query->paginate(25)->withQueryString();
        $users = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('audit.index', compact('logs', 'users'));
    }

    public function show(Audit $audit)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('audit.show', compact('audit'));
    }

    public function export(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $request->validate(['format' => 'required|in:xlsx,csv']);

        $filename = 'audit_logs_' . now()->format('Ymd_His') . '.' . $request->format;

        return Excel::download(new AuditLogsExport($request->only(['user_id', 'event', 'date_from', 'date_to'])), $filename);
    }
}
