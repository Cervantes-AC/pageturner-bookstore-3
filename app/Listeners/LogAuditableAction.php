<?php

namespace App\Listeners;

use App\Events\AuditableActionPerformed;
use App\Models\AuditLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogAuditableAction
{
    public function handle(AuditableActionPerformed $event): void
    {
        if (!Auth::check()) {
            return;
        }

        $auditId = (string) Str::uuid();
        $oldValues = AuditLog::sanitizeValues($event->oldValues);
        $newValues = AuditLog::sanitizeValues($event->newValues);
        $auditableType = get_class($event->model);
        $auditableId = $event->model->id;
        $checksum = AuditLog::checksumFor(
            $auditId,
            Auth::id(),
            $event->event,
            $auditableType,
            $auditableId,
            $oldValues,
            $newValues
        );

        AuditLog::create([
            'id' => $auditId,
            'user_id' => Auth::id(),
            'event' => $event->event,
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'checksum' => $checksum,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'description' => $event->description,
        ]);
    }
}
