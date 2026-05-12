<?php

namespace App\Observers;

use App\Models\Book;
use App\Services\BookCacheService;

class BookObserver
{
    public function __construct(
        private readonly BookCacheService $cacheService
    ) {}

    public function saved(Book $book): void
    {
        $this->invalidateAll($book);
    }

    public function deleted(Book $book): void
    {
        $this->invalidateAll($book);
    }

    private function invalidateAll(Book $book): void
    {
        $this->cacheService->invalidateCatalog();
        $this->cacheService->invalidateIsbn($book->isbn);
        $this->cacheService->invalidateCategory($book->category_id);
    }
}
