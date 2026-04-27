<?php

namespace App\Observers;

use App\Models\Book;
use App\Services\BookCacheService;

/**
 * Lab Activity 7 — Step 5.3
 * Listens to Book model events and invalidates relevant Redis cache tags.
 *
 * Registered in AppServiceProvider::boot() via Book::observe(BookObserver::class).
 *
 * Events handled:
 *   saved()   → flush catalog + category + ISBN caches
 *   deleted() → flush catalog + category + ISBN caches
 */
class BookObserver
{
    public function __construct(private BookCacheService $cache) {}

    /**
     * Fires after create OR update.
     */
    public function saved(Book $book): void
    {
        $this->invalidate($book);
    }

    /**
     * Fires after delete.
     */
    public function deleted(Book $book): void
    {
        $this->invalidate($book);
    }

    private function invalidate(Book $book): void
    {
        // Flush all catalog pages
        $this->cache->invalidateCatalog();

        // Flush this book's ISBN cache
        $this->cache->invalidateIsbn($book->isbn);

        // Flush the category this book belongs to
        if ($book->category_id) {
            $this->cache->invalidateCategory($book->category_id);
        }
    }
}
