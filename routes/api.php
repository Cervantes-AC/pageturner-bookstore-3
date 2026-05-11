<?php

use App\Http\Controllers\Api\BookApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProfileApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — PageTurner Bookstore
|--------------------------------------------------------------------------
|
| Tiered throttling:  public(30/min), standard(60/min), premium(300/min),
|                     admin(1000/min), auth(10/min), search(30/min)
|
*/

// ── Health Check ─────────────────────────────────────────────
Route::get('/health', function () {
    return response()->json([
        'status'    => 'ok',
        'timestamp' => now()->toIso8601String(),
        'version'   => '7.0.0',
    ]);
});

// ── Public API (throttle: public) ────────────────────────────
Route::middleware(['throttle:public', 'api.transform'])->group(function () {
    Route::get('/books', [BookApiController::class, 'index']);
    Route::get('/books/{book}', [BookApiController::class, 'show']);
    Route::get('/categories', [CategoryApiController::class, 'index']);
    Route::get('/categories/{category}', [CategoryApiController::class, 'show']);
});

// ── Authenticated API ────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('throttle:standard')->group(function () {
        Route::get('/profile', [ProfileApiController::class, 'show']);
        Route::put('/profile', [ProfileApiController::class, 'update']);
        Route::get('/orders', [OrderApiController::class, 'index']);
        Route::get('/orders/{order}', [OrderApiController::class, 'show']);
        Route::post('/orders', [OrderApiController::class, 'store']);
    });

    // Tiered search endpoint (Redis-backed rate limiting)
    Route::get('/search/books', [BookApiController::class, 'index'])
        ->middleware('throttle:search');

    // Admin endpoints (throttle: admin)
    Route::middleware('throttle:admin')->group(function () {
        Route::post('/admin/books', [BookApiController::class, 'store']);
        Route::put('/admin/books/{book}', [BookApiController::class, 'update']);
        Route::delete('/admin/books/{book}', [BookApiController::class, 'destroy']);
        Route::get('/admin/orders', [OrderApiController::class, 'index']);
    });
});

// ── Auth endpoints (throttle: auth) ──────────────────────────
require __DIR__.'/auth.php';
