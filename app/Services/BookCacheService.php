<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Lab Activity 7 — Step 5
 * Redis caching abstraction with tag-based invalidation.
 *
 * Cache tag strategy:
 *   - 'books'              : all book-related cache entries
 *   - 'category:{id}'      : entries specific to one category
 *   - 'isbn:{isbn}'        : individual ISBN lookups
 *
 * Note: Cache tagging requires Redis or Memcached.
 *       Falls back to plain cache keys on file/database drivers.
 *
 * TTLs:
 *   - Catalog pages  : 5 minutes  (300s)
 *   - ISBN lookups   : 30 minutes (1800s)
 *   - Category pages : 10 minutes (600s)
 *   - Bestsellers    : 1 hour     (3600s)
 */
class BookCacheService
{
    private const TTL_CATALOG    = 300;
    private const TTL_ISBN       = 1800;
    private const TTL_CATEGORY   = 600;
    private const TTL_BESTSELLER = 3600;

    // ── Cache Reads ───────────────────────────────────────────────────────────

    public function rememberCatalog(string $key, callable $callback): mixed
    {
        return $this->tagged(['books'], fn($store) =>
            $store->remember($key, self::TTL_CATALOG, $callback)
        );
    }

    public function rememberIsbn(string $isbn, callable $callback): mixed
    {
        return $this->tagged(['books', "isbn:{$isbn}"], fn($store) =>
            $store->remember("book:isbn:{$isbn}", self::TTL_ISBN, $callback)
        );
    }

    public function rememberCategory(int $categoryId, callable $callback): mixed
    {
        return $this->tagged(['books', "category:{$categoryId}"], fn($store) =>
            $store->remember("category:{$categoryId}:catalog", self::TTL_CATEGORY, $callback)
        );
    }

    public function rememberBestsellers(callable $callback): mixed
    {
        return $this->tagged(['books', 'bestsellers'], fn($store) =>
            $store->remember('books:bestsellers', self::TTL_BESTSELLER, $callback)
        );
    }

    // ── Cache Invalidation ────────────────────────────────────────────────────

    /**
     * Flush all book-related cache entries.
     * Called by BookObserver on any book save/delete.
     */
    public function invalidateCatalog(): void
    {
        $this->flushTag('books');
    }

    /**
     * Flush cache for a specific ISBN.
     */
    public function invalidateIsbn(string $isbn): void
    {
        Cache::forget("book:isbn:{$isbn}");
        $this->flushTag("isbn:{$isbn}");
    }

    /**
     * Flush cache for a specific category.
     */
    public function invalidateCategory(int $categoryId): void
    {
        $this->flushTag("category:{$categoryId}");
    }

    // ── Internals ─────────────────────────────────────────────────────────────

    /**
     * Execute a callback with a tagged cache store.
     * Falls back to plain Cache facade if tagging is not supported
     * (file/database drivers).
     */
    private function tagged(array $tags, callable $callback): mixed
    {
        try {
            return $callback(Cache::tags($tags));
        } catch (\BadMethodCallException) {
            // Cache driver does not support tagging — use plain cache
            return $callback(Cache::store());
        }
    }

    /**
     * Flush a cache tag if supported.
     */
    private function flushTag(string $tag): void
    {
        try {
            Cache::tags([$tag])->flush();
        } catch (\BadMethodCallException) {
            // Tagging not supported — nothing to flush
        }
    }
}
