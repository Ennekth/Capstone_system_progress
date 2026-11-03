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

    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);

        $old = $log->old_values ?? $log->properties['old'] ?? [];
        $new = $log->new_values ?? $log->properties['attributes'] ?? $log->properties ?? [];

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $log->user->full_name ?? $log->user->name ?? 'System / Guest',
                'event' => ucfirst($log->event),
                'target_model' => class_basename($log->auditable_type) . ' #' . $log->auditable_id,
                'ip_address' => $log->ip_address ?? '—',
                'timestamp' => $log->created_at->format('M d, Y h:i A'),
                'changes' => $this->formatChangesForHumans($old, $new, $log->event)
            ]
        ]);
    }

    private function formatChangesForHumans($old, $new, $event)
    {
        $ignoredKeys = ['id', 'created_at', 'updated_at', 'password', 'remember_token'];
        $changes = [];

        if ($event === 'created' && is_array($new)) {
            foreach ($new as $key => $value) {
                if (in_array($key, $ignoredKeys)) continue;
                $changes[] = [
                    'field' => ucwords(str_replace('_', ' ', $key)),
                    'old' => null,
                    'new' => is_array($value) ? json_encode($value) : ($value ?? '—')
                ];
            }
        } elseif ($event === 'updated') {
            $allKeys = array_unique(array_merge(array_keys(is_array($old) ? $old : []), array_keys(is_array($new) ? $new : [])));
            foreach ($allKeys as $key) {
                if (in_array($key, $ignoredKeys)) continue;
                $oldVal = $old[$key] ?? null;
                $newVal = $new[$key] ?? null;
                
                // Only show fields that actually changed
                if ($oldVal !== $newVal) {
                    $changes[] = [
                        'field' => ucwords(str_replace('_', ' ', $key)),
                        'old' => is_array($oldVal) ? json_encode($oldVal) : ($oldVal ?? 'None'),
                        'new' => is_array($newVal) ? json_encode($newVal) : ($newVal ?? 'None')
                    ];
                }
            }
        } else {
            // Fallback for general properties/payloads
            $data = is_array($new) && !empty($new) ? $new : (is_array($old) ? $old : []);
            foreach ($data as $key => $value) {
                if (in_array($key, $ignoredKeys)) continue;
                $changes[] = [
                    'field' => ucwords(str_replace('_', ' ', $key)),
                    'old' => '—',
                    'new' => is_array($value) ? json_encode($value) : ($value ?? '—')
                ];
            }
        }

        return $changes;
    }
}