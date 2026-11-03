<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait AuditsChanges
{
    public static function bootAuditsChanges()
    {
        static::created(function ($model) {
            static::logAudit('created', $model, null, $model->getAttributes());
        });

        static::updated(function ($model) {
            static::logAudit('updated', $model, $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            static::logAudit('deleted', $model, $model->getAttributes(), null);
        });
    }

    protected static function logAudit($event, $model, $oldValues, $newValues)
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'event' => $event,
                'auditable_type' => get_class($model),
                'auditable_id' => $model->getKey(),
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            // Fails gracefully if run during database migrations or console seeding
        }
    }
}