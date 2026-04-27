<?php

namespace App\Repositories;

use App\Models\Book;
use App\Services\BookCacheService;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Lab Activity 7 — Step 4
 * Optimized data access layer for the books catalog.
 *
 * Strategies applied:
 *  - Cursor pagination  : O(1) vs OFFSET's O(n) — critical at 1M+ rows
 *  - Column selection   : only fetch columns needed by the view
 *  - Relation limiting  : with(['category:id,name']) — never eager-load full models
 *  - Cache tagging      : Redis tag-based caching with targeted invalidation
 *  - Covering index use : query columns match idx_books_catalog_filter
 */
class BookRepository
{
    public function __construct(private BookCacheService $cache) {}

    // ── Public Catalog ────────────────────────────────────────────────────────

    /**
     * Paginated active catalog with cursor pagination.
     * Hits idx_books_catalog_filter covering index.
     */
    public function activeCatalog(array $filters = [], int $perPage = 100): CursorPaginator
    {
        $cacheKey = 'catalog:' . md5(serialize($filters) . $perPage);

        // Cache catalog pages for 5 minutes
        return $this->cache->rememberCatalog($cacheKey, function () use ($filters, $perPage) {
            $query = Book::select([
                    'books.id', 'books.isbn', 'books.title', 'books.author',
                    'books.publisher', 'books.price', 'books.stock_quantity',
                    'books.published_at', 'books.category_id', 'books.format',
                    'books.is_featured',
                ])
                ->with(['category:id,name'])
                ->where('is_active', true);

            // Apply filters
            if (!empty($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }
            if (!empty($filters['min_price'])) {
                $query->where('price', '>=', $filters['min_price']);
            }
            if (!empty($filters['max_price'])) {
                $query->where('price', '<=', $filters['max_price']);
            }
            if (!empty($filters['format'])) {
                $query->where('format', $filters['format']);
            }
            if (!empty($filters['search'])) {
                $term = $filters['search'];
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', "%{$term}%")
                      ->orWhere('author', 'like', "%{$term}%")
                      ->orWhere('isbn', 'like', "%{$term}%");
                });
            }

            // Stable sort required for cursor pagination
            return $query
                ->orderBy('published_at', 'desc')
                ->orderBy('id', 'desc')
                ->cursorPaginate($perPage);
        });
    }

    /**
     * ISBN exact-match lookup — hits unique index, cached in Redis.
     * Target: < 50 ms
     */
    public function findByIsbn(string $isbn): ?Book
    {
        return $this->cache->rememberIsbn($isbn, function () use ($isbn) {
            return Book::select([
                    'id', 'isbn', 'title', 'author', 'publisher',
                    'price', 'stock_quantity', 'category_id', 'format',
                    'published_at', 'description', 'is_active',
                ])
                ->with(['category:id,name'])
                ->where('isbn', $isbn)
                ->first();
        });
    }

    /**
     * Category filter — hits idx_books_catalog_filter composite index.
     * Target: < 150 ms for 100K+ results
     */
    public function byCategory(int $categoryId, int $perPage = 100): CursorPaginator
    {
        return $this->cache->rememberCategory($categoryId, function () use ($categoryId, $perPage) {
            return Book::select([
                    'id', 'isbn', 'title', 'author', 'price',
                    'stock_quantity', 'published_at', 'category_id', 'format',
                ])
                ->where('category_id', $categoryId)
                ->where('is_active', true)
                ->orderBy('published_at', 'desc')
                ->orderBy('id', 'desc')
                ->cursorPaginate($perPage);
        });
    }

    /**
     * Full-text search using LIKE on SQLite, MATCH AGAINST on MySQL.
     * Target: < 300 ms on 1M records
     */
    public function fullTextSearch(string $term, int $perPage = 50): CursorPaginator
    {
        $driver = DB::getDriverName();

        $query = Book::select([
                'id', 'isbn', 'title', 'author', 'price',
                'stock_quantity', 'category_id', 'format',
            ])
            ->with(['category:id,name'])
            ->where('is_active', true);

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // Use MySQL FULLTEXT index (idx_books_fulltext)
            $query->whereRaw('MATCH(title, description) AGAINST(? IN BOOLEAN MODE)', [$term]);
        } else {
            // SQLite fallback — LIKE search
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('author', 'like', "%{$term}%");
            });
        }

        return $query->orderBy('id', 'desc')->cursorPaginate($perPage);
    }

    /**
     * Chunked export using lazy collection — memory-safe for 1M+ records.
     * Yields records in chunks without loading all into memory.
     */
    public function exportChunked(array $filters = [], int $chunkSize = 2000): \Generator
    {
        $query = Book::select([
                'isbn', 'title', 'author', 'publisher', 'format',
                'price', 'stock_quantity', 'published_at',
            ])
            ->where('is_active', true)
            ->orderBy('id');

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        foreach ($query->lazy($chunkSize) as $book) {
            yield $book;
        }
    }
}
