<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\AuditLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

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
                $checksum = hash('sha256', $auditId . $request->user()->id . $routeName);

                AuditLog::create([
                    'id' => $auditId,
                    'user_id' => $request->user()->id,
                    'event' => $auditableEvents[$routeName],
                    'auditable_type' => 'App\\Models\\' . $this->guessModel($routeName),
                    'auditable_id' => $route?->parameter('book') ?? $route?->parameter('category') ?? $route?->parameter('order') ?? $route?->parameter('review') ?? 0,
                    'old_values' => null,
                    'new_values' => $request->except(['_token', '_method', 'password', 'password_confirmation']),
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
}
