<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;

class BookCacheService
{
    private const CATALOG_CACHE_KEY = 'books:catalog';
    private const ISBN_CACHE_PREFIX = 'book:isbn:';
    private const CACHE_TTL = 3600;
    private const TAG_CACHE_STORE = 'redis-tags';

    private function tags(array $tags): \Illuminate\Cache\TaggedCache
    {
        return Cache::store(self::TAG_CACHE_STORE)->tags($tags);
    }

    public function rememberIsbn(string $isbn, Closure $callback): mixed
    {
        return Cache::remember(
            self::ISBN_CACHE_PREFIX . $isbn,
            self::CACHE_TTL,
            $callback
        );
    }

    public function forgetIsbn(string $isbn): void
    {
        Cache::forget(self::ISBN_CACHE_PREFIX . $isbn);
    }

    public function rememberCatalog(string $cursorHash, Closure $callback): mixed
    {
        return $this->tags(['catalog'])
            ->remember(
                self::CATALOG_CACHE_KEY . ":{$cursorHash}",
                self::CACHE_TTL,
                $callback
            );
    }

    public function rememberCategoryCatalog(int $categoryId, string $cursorHash, Closure $callback): mixed
    {
        return $this->tags(["category:{$categoryId}"])
            ->remember(
                "category:{$categoryId}:catalog:{$cursorHash}",
                self::CACHE_TTL,
                $callback
            );
    }

    public function invalidateCatalog(): void
    {
        $this->tags(['catalog'])->flush();
    }

    public function invalidateCategory(int $categoryId): void
    {
        $this->tags(["category:{$categoryId}"])->flush();
        Cache::forget(self::ISBN_CACHE_PREFIX . ':category:' . $categoryId);
    }

    public function invalidateIsbn(string $isbn): void
    {
        Cache::forget(self::ISBN_CACHE_PREFIX . $isbn);
    }

    public function warmCategoryPopular(int $categoryId, array $books): void
    {
        $this->tags(["category:{$categoryId}"])
            ->put(
                "category:{$categoryId}:popular",
                $books,
                7200
            );
    }

    public function getCategoryPopular(int $categoryId): mixed
    {
        return Cache::get("category:{$categoryId}:popular");
    }

    public function flushAll(): void
    {
        $this->tags(['catalog', 'category'])->flush();
    }
}
