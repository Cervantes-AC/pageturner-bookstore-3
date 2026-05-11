<?php

namespace App\Repositories;

use App\Models\Book;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Cache;

class BookRepository
{
    private const ALLOWED_LIST_FIELDS = [
        'books.id', 'books.isbn', 'books.title', 'books.author',
        'books.price', 'books.stock_quantity', 'books.published_at',
        'books.category_id', 'books.cover_image_url', 'books.format',
    ];

    public function getActiveCatalog(int $perPage = 100): CursorPaginator
    {
        return Book::select(self::ALLOWED_LIST_FIELDS)
            ->with(['category:id,name,slug'])
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage);
    }

    public function findByIsbn(string $isbn): ?Book
    {
        $cacheKey = "book:isbn:{$isbn}";

        return Cache::remember($cacheKey, 3600, function () use ($isbn) {
            return Book::select(self::ALLOWED_LIST_FIELDS)
                ->with(['category:id,name,slug'])
                ->where('isbn', $isbn)
                ->first();
        });
    }

    public function findByCategory(int $categoryId, int $perPage = 100): CursorPaginator
    {
        return Book::select(self::ALLOWED_LIST_FIELDS)
            ->with(['category:id,name,slug'])
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage);
    }

    public function search(string $query, int $perPage = 50): CursorPaginator
    {
        return Book::search($query)
            ->query(function ($builder) {
                $builder->select(self::ALLOWED_LIST_FIELDS)
                    ->with(['category:id,name,slug']);
            })
            ->cursorPaginate($perPage);
    }

    public function getExportQuery()
    {
        return Book::query()
            ->select([
                'isbn', 'title', 'author', 'price',
                'stock_quantity', 'published_at', 'format',
            ])
            ->where('is_active', true)
            ->orderBy('id');
    }
}
