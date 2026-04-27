<?php

namespace App\Jobs;

use App\Models\Book;
use App\Services\BookCacheService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Lab Activity 7 — Step 5.2
 * Asynchronous cache warmup for a single category.
 *
 * Pre-loads the top 1,000 active books for a category into Redis
 * so the first real user request is a cache hit, not a cold query.
 *
 * Dispatch after seeding:
 *   \App\Models\Category::all()->each(fn($c) => WarmCategoryCache::dispatch($c->id));
 *
 * Or dispatch on a schedule (see routes/console.php).
 */
class WarmCategoryCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(public readonly int $categoryId) {}

    public function handle(BookCacheService $cache): void
    {
        $books = Book::select([
                'id', 'title', 'author', 'price',
                'stock_quantity', 'published_at', 'format',
            ])
            ->where('category_id', $this->categoryId)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->limit(1000)
            ->get();

        try {
            Cache::tags(["category:{$this->categoryId}"])
                ->put(
                    "category:{$this->categoryId}:popular",
                    $books,
                    7200 // 2 hours
                );
        } catch (\BadMethodCallException) {
            // Fallback for non-tagging drivers
            Cache::put("category:{$this->categoryId}:popular", $books, 7200);
        }

        Log::info("WarmCategoryCache: warmed category {$this->categoryId} ({$books->count()} books)");
    }
}
