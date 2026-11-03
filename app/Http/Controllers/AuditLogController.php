<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Live Search Logic matching other modules
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('event', 'like', "%{$search}%")
                  ->orWhere('auditable_type', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%");
                  });
            });
        }

        // Dropdown Event Filter Logic
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        $logs = $query->paginate(15)->withQueryString();

        return view('audit-logs.index', compact('logs'));
    }
}