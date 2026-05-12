<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;

// ─── Rate Limiter Registration ──────────────────────────────
RateLimiter::for('api', function (Request $request) {
    $user = $request->user();
    $tier = $user ? $user->getRateLimitTier() : 'public';
    $limits = config("rate-limiting.tiers.{$tier}", ['limit' => 30, 'decay' => 60]);

    return \Illuminate\Cache\RateLimiting\Limit::perMinutes($limits['decay'] ?? 60, $limits['limit'])
        ->by($user ? $user->id : $request->ip())
        ->response(function () use ($limits) {
            return response()->json([
                'message' => 'Too many requests.',
                'limit' => $limits['limit'],
            ], 429);
        });
});

// ─── Public API Routes ──────────────────────────────────────
Route::middleware('throttle:api')->group(function () {

    // Books
    Route::get('/books', function (Request $request) {
        $query = Book::with('category');
        if ($request->filled('category')) $query->where('category_id', $request->category);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")->orWhere('author', 'like', "%{$s}%");
            });
        }
        return response()->json($query->paginate($request->per_page ?? 20));
    });

    Route::get('/books/{book}', function (Book $book) {
        return response()->json($book->load('category', 'reviews.user'));
    });

    // Categories
    Route::get('/categories', function () {
        return response()->json(Category::withCount('books')->get());
    });

    Route::get('/categories/{category}', function (Category $category) {
        return response()->json($category->load('books'));
    });

    // Audit logs (admin only)
    Route::middleware('auth')->get('/audit-logs', function (Request $request) {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        $query = AuditLog::with('user');
        if ($request->filled('event')) $query->where('event', $request->event);
        return response()->json($query->latest()->paginate(20));
    });
});

// ─── Authenticated API Routes ──────────────────────────────
Route::middleware(['auth', 'throttle:api'])->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    Route::get('/user/orders', function (Request $request) {
        return response()->json($request->user()->orders()->with('orderItems.book')->latest()->paginate(20));
    });
});

// ─── Auth routes (strict rate limit) ───────────────────────
Route::post('/login', function (Request $request) {
    return response()->json(['message' => 'Login endpoint']);
})->middleware('throttle:10,1');
