<?php

use App\Http\Resources\BookResource;
use App\Http\Resources\CategoryResource;
use App\Models\AuditLog;
use App\Models\Book;
use App\Models\Category;
use App\Repositories\BookRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;

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

Route::middleware('throttle:api')->group(function () {

    Route::get('/books', function (Request $request, BookRepository $repository) {
        if ($request->filled('isbn')) {
            $book = $repository->findByIsbn($request->isbn);
            if (!$book) {
                return response()->json(['message' => 'Book not found'], 404);
            }
            return response()->json(new BookResource($book));
        }

        if ($request->filled('search')) {
            $results = $repository->searchByFulltext($request->search, (int)($request->per_page ?? 50));
            return BookResource::collection($results)->response();
        }

        if ($request->filled('category')) {
            $results = $repository->getCatalogByCategory(
                (int)$request->category,
                (int)($request->per_page ?? 100)
            );
            return BookResource::collection($results)->response();
        }

        if ($request->filled('min_price') || $request->filled('max_price')) {
            $min = (float)($request->min_price ?? 0);
            $max = (float)($request->max_price ?? 999999);
            $results = $repository->getBooksByPriceRange($min, $max, (int)($request->per_page ?? 100));
            return BookResource::collection($results)->response();
        }

        $results = $repository->getActiveCatalog((int)($request->per_page ?? 100));
        return BookResource::collection($results)->response();
    });

    Route::get('/books/{book}', function (Book $book, BookRepository $repository) {
        $book = $repository->findById($book->id);
        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }
        return response()->json(new BookResource($book));
    });

    Route::get('/categories', function () {
        $categories = Category::withCount('books')->get();
        return CategoryResource::collection($categories);
    });

    Route::get('/categories/{category}', function (Category $category) {
        $category->loadCount('books');
        $books = $category->books()
            ->select(['id', 'isbn', 'title', 'author', 'price', 'format', 'stock_quantity', 'cover_image', 'published_at'])
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->cursorPaginate(100);

        return response()->json([
            'category' => new CategoryResource($category),
            'books' => BookResource::collection($books),
        ]);
    });

    Route::get('/bestseller-stats', function () {
        $stats = \Illuminate\Support\Facades\DB::table('mv_bestseller_stats')
            ->join('categories', 'mv_bestseller_stats.category_id', '=', 'categories.id')
            ->select([
                'mv_bestseller_stats.*',
                'categories.name as category_name',
            ])
            ->orderBy('total_books', 'desc')
            ->get();

        return response()->json($stats);
    });

    Route::middleware('auth')->get('/audit-logs', function (Request $request) {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        $query = AuditLog::with('user');
        if ($request->filled('event')) $query->where('event', $request->event);
        return response()->json($query->latest()->paginate(20));
    });
});

Route::middleware(['auth', 'throttle:api'])->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    Route::get('/user/orders', function (Request $request) {
        return response()->json(
            $request->user()->orders()->with('orderItems.book')->latest()->paginate(20)
        );
    });
});

Route::post('/login', function (Request $request) {
    return response()->json(['message' => 'Login endpoint']);
})->middleware('throttle:10,1');
