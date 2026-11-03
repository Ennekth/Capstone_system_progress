<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

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
                'target_model' => $this->getSpecificTargetModel($log),
                'direction_context' => $this->getDirectionContext($log, $old, $new),
                'description' => $this->generateSummaryDescription($log->event, $log->auditable_type, $old, $new),
                'ip_address' => $log->ip_address ?? '—',
                'timestamp' => $log->created_at->format('M d, Y h:i A'),
                'changes' => $this->formatChangesForHumans($old, $new, $log->event)
            ]
        ]);
    }

    /**
     * Resolve a specific target model name (e.g., Product #5 - Aqualife Water Bottle).
     */
    private function getSpecificTargetModel($log)
    {
        if (!$log->auditable_type || !$log->auditable_id) {
            if ($log->event === 'login' || $log->event === 'logout') {
                return 'User Authentication Session';
            }
            return 'System Event';
        }

        $modelClass = $log->auditable_type;
        $modelName = class_basename($modelClass);
        $instance = class_exists($modelClass) ? $modelClass::find($log->auditable_id) : null;

        $identifier = '';
        if ($instance) {
            if (isset($instance->product_name)) {
                $identifier = ' (' . $instance->product_name . ')';
            } elseif (isset($instance->name)) {
                $identifier = ' (' . $instance->name . ')';
            } elseif (isset($instance->title)) {
                $identifier = ' (' . $instance->title . ')';
            }
        }

        return "{$modelName} #{$log->auditable_id}{$identifier}";
    }

    /**
     * Determine where the action/change originated and where it applied.
     */
    private function getDirectionContext($log, $old, $new)
    {
        $payload = array_merge(is_array($old) ? $old : [], is_array($new) ? $new : []);

        if (isset($payload['location'])) {
            return 'Location: ' . ucfirst($payload['location']);
        }
        if (isset($payload['direction'])) {
            return 'Transfer Direction: ' . str_replace('_', ' ', ucwords($payload['direction'], '_'));
        }
        if ($log->event === 'login') {
            return 'Origin: Web Login Portal';
        }
        if ($log->event === 'logout') {
            return 'Origin: User Session Terminated';
        }

        return 'Origin: Web Dashboard Interface';
    }

    private function generateSummaryDescription($event, $auditableType, $old, $new)
    {
        $action = ucfirst($event);

        if ($event === 'login') {
            return "User successfully logged into the inventory and POS system.";
        } elseif ($event === 'logout') {
            return "User logged out of the system session.";
        } elseif ($event === 'created') {
            return "A new record was successfully created and added to the database.";
        } elseif ($event === 'updated') {
            return "An existing record was modified with updated attributes.";
        } elseif ($event === 'deleted') {
            return "A record was deleted from the system.";
        }

        return "A '{$action}' event was performed.";
    }

    private function formatChangesForHumans($old, $new, $event)
    {
        $ignoredKeys = ['id', 'created_at', 'updated_at', 'password', 'remember_token'];
        $changes = [];

        if ($event === 'login' || $event === 'logout') {
            return [[
                'field' => 'Authentication Status',
                'old' => 'Inactive',
                'new' => $event === 'login' ? 'Active Session Started' : 'Session Ended'
            ]];
        }

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
                
                if ($oldVal !== $newVal) {
                    $changes[] = [
                        'field' => ucwords(str_replace('_', ' ', $key)),
                        'old' => is_array($oldVal) ? json_encode($oldVal) : ($oldVal ?? 'None'),
                        'new' => is_array($newVal) ? json_encode($newVal) : ($newVal ?? 'None')
                    ];
                }
            }
        } else {
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