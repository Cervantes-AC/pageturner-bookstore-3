<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Facades\Cache;

class BookCacheService
{
    public function invalidateCatalog(): void
    {
        Cache::tags(['catalog'])->flush();
    }

    public function invalidateIsbn(string $isbn): void
    {
        Cache::forget("book:isbn:{$isbn}");
    }

    public function invalidateCategory(int $categoryId): void
    {
        Cache::tags(["category:{$categoryId}"])->flush();
    }

    public function warmCategory(int $categoryId, int $limit = 1000): void
    {
        $books = Book::select([
            'id', 'title', 'author', 'price',
            'stock_quantity', 'cover_image_url', 'format',
        ])
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        Cache::tags(["category:{$categoryId}"])
            ->put("category:{$categoryId}:popular", $books, now()->addHours(2));
    }
}
