<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\AuditLog;
use Illuminate\Support\Str;

class AuditRequests
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        if ($request->user() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $route = $request->route();
            $routeName = $route ? $route->getName() : '';

            $auditableEvents = [
                'admin.books.store' => 'created',
                'admin.books.update' => 'updated',
                'admin.books.destroy' => 'deleted',
                'admin.categories.store' => 'created',
                'admin.categories.update' => 'updated',
                'admin.categories.destroy' => 'deleted',
                'orders.store' => 'order_placed',
                'orders.updateStatus' => 'order_status_updated',
                'reviews.store' => 'review_created',
                'reviews.destroy' => 'review_deleted',
            ];

            if (isset($auditableEvents[$routeName])) {
                $auditId = (string) Str::uuid();
                $event = $auditableEvents[$routeName];
                $auditableType = 'App\\Models\\' . $this->guessModel($routeName);
                $auditableId = $this->auditableId($route);
                $newValues = AuditLog::sanitizeValues(
                    $request->except(['_token', '_method'])
                );
                $checksum = AuditLog::checksumFor(
                    $auditId,
                    $request->user()->id,
                    $event,
                    $auditableType,
                    $auditableId,
                    null,
                    $newValues
                );

                AuditLog::create([
                    'id' => $auditId,
                    'user_id' => $request->user()->id,
                    'event' => $event,
                    'auditable_type' => $auditableType,
                    'auditable_id' => $auditableId,
                    'old_values' => null,
                    'new_values' => $newValues,
                    'checksum' => $checksum,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                ]);
            }
        }
    }

    protected function guessModel($routeName)
    {
        if (str_contains($routeName, 'books')) return 'Book';
        if (str_contains($routeName, 'categories')) return 'Category';
        if (str_contains($routeName, 'orders')) return 'Order';
        if (str_contains($routeName, 'reviews')) return 'Review';
        return 'Unknown';
    }

    protected function auditableId($route): int
    {
        foreach (['book', 'category', 'order', 'review'] as $parameter) {
            $value = $route?->parameter($parameter);

            if (is_object($value) && isset($value->id)) {
                return (int) $value->id;
            }

            if (is_numeric($value)) {
                return (int) $value;
            }
        }

        return 0;
    }
}
