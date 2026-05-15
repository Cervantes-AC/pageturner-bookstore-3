<?php

use App\Http\Middleware\ApiRateLimiter;
use App\Http\Middleware\AuditRequests;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\FieldFiltering;
use App\Http\Middleware\RequireTwoFactor;
use App\Http\Middleware\TransformApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api-rate-limiter' => ApiRateLimiter::class,
            'audit.requests' => AuditRequests::class,
            'transform.api' => TransformApiResponse::class,
            'field.filtering' => FieldFiltering::class,
            'role' => CheckRole::class,
            '2fa' => RequireTwoFactor::class,
            'verified' => EnsureEmailIsVerified::class,
        ]);

        $middleware->api(append: [
            'transform.api',
            'field.filtering',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
