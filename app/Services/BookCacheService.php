<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;

class BookCacheService
{
    private const CATALOG_CACHE_KEY = 'books:catalog';
    private const ISBN_CACHE_PREFIX = 'book:isbn:';
    private const CACHE_TTL = 3600;

    public function rememberIsbn(string $isbn, Closure $callback): mixed
    {
        return Cache::remember(
            self::ISBN_CACHE_PREFIX . $isbn,
            self::CACHE_TTL,
            $callback
        );
    }

    public function rememberCatalog(int $page, Closure $callback): mixed
    {
        return Cache::tags(['books:catalog'])
            ->remember(
                self::CATALOG_CACHE_KEY . ":page:{$page}",
                self::CACHE_TTL,
                $callback
            );
    }

    public function rememberCategoryCatalog(int $categoryId, int $page, Closure $callback): mixed
    {
        return Cache::tags(["category:{$categoryId}"])
            ->remember(
                "category:{$categoryId}:catalog:page:{$page}",
                self::CACHE_TTL,
                $callback
            );
    }

    public function invalidateCatalog(): void
    {
        if (Cache::supportsTags()) {
            Cache::tags(['books:catalog'])->flush();
        }
    }

    public function invalidateCategory(int $categoryId): void
    {
        if (Cache::supportsTags()) {
            Cache::tags(["category:{$categoryId}"])->flush();
        }
    }

    public function invalidateIsbn(string $isbn): void
    {
        Cache::forget(self::ISBN_CACHE_PREFIX . $isbn);
    }

    public function warmCategoryPopular(int $categoryId, array $books): void
    {
        if (Cache::supportsTags()) {
            Cache::tags(["category:{$categoryId}"])
                ->put(
                    "category:{$categoryId}:popular",
                    $books,
                    7200
                );
        }
    }

    public function getCategoryPopular(int $categoryId): mixed
    {
        return Cache::get("category:{$categoryId}:popular");
    }
}
