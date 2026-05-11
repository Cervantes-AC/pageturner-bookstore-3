<?php

namespace App\Observers;

use App\Models\Book;
use App\Services\BookCacheService;

class BookObserver
{
    public function __construct(
        private readonly BookCacheService $cacheService,
    ) {}

    public function saved(Book $book): void
    {
        $this->cacheService->invalidateCatalog();
        $this->cacheService->invalidateIsbn($book->isbn);
        if ($book->category_id) {
            $this->cacheService->invalidateCategory($book->category_id);
        }
    }

    public function deleted(Book $book): void
    {
        $this->cacheService->invalidateCatalog();
        $this->cacheService->invalidateIsbn($book->isbn);
        if ($book->category_id) {
            $this->cacheService->invalidateCategory($book->category_id);
        }
    }
}
