<?php

use App\Http\Controllers\Api\BookApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProfileApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Tiered Rate Limiting & Data Transformation
|--------------------------------------------------------------------------
|
| These routes demonstrate:
|   - Tiered rate limiting (public/standard/premium/admin)
|   - API transform middleware (snake_case → camelCase, field filtering)
|   - Cursor-based pagination for large collections
|   - ETag support for conditional requests
|
| Rate limit tiers (configured in AppServiceProvider):
|   public:    30 req/min,  5 req/sec burst    (unauthenticated)
|   standard:  60 req/min,  5 req/sec burst    (regular customers)
|   premium:  300 req/min, 10 req/sec burst    (premium/VIP customers)
|   admin:   1000 req/min, 20 req/sec burst    (administrators)
|   auth:      10 req/min,  2 req/sec burst    (login/register endpoints)
*/

// ── Health check (public) ────────────────────────────────────
Route::get('/health', function () {
    return response()->json([
        'status'  => 'ok',
        'time'    => now()->toIso8601String(),
        'version' => '1.0.0',
    ]);
})->middleware('throttle:public');

// ── Public API (browsing, no auth required) ──────────────────
Route::middleware(['throttle:public', 'api.transform'])->group(function () {
    Route::get('/books', [BookApiController::class, 'index']);
    Route::get('/books/{book}', [BookApiController::class, 'show']);
    Route::get('/categories', [CategoryApiController::class, 'index']);
    Route::get('/categories/{category}', [CategoryApiController::class, 'show']);
});

// ── Authenticated API (requires auth) ────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Profile
    Route::middleware(['throttle:standard', 'api.transform'])->prefix('profile')->group(function () {
        Route::get('/', [ProfileApiController::class, 'show']);
        Route::put('/', [ProfileApiController::class, 'update']);
    });

    // Orders (authenticated users)
    Route::middleware(['throttle:standard', 'api.transform'])->group(function () {
        Route::get('/orders', [OrderApiController::class, 'index']);
        Route::get('/orders/{order}', [OrderApiController::class, 'show']);
        Route::post('/orders', [OrderApiController::class, 'store']);
    });

    // Admin-only endpoints
    Route::middleware('throttle:admin')->prefix('admin')->group(function () {
        Route::post('/books', [BookApiController::class, 'store'])->middleware('api.transform');
        Route::put('/books/{book}', [BookApiController::class, 'update'])->middleware('api.transform');
        Route::delete('/books/{book}', [BookApiController::class, 'destroy']);
        Route::get('/orders', [OrderApiController::class, 'adminIndex'])->middleware('api.transform');
    });
});

// ── Auth endpoints (strict rate limiting) ────────────────────
Route::middleware('throttle:auth')->group(function () {
    // These are handled by the web routes, but defined here for documentation
});
