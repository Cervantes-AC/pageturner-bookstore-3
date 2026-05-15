<?php

namespace App\Repositories;

use App\Models\Book;
use App\Services\BookCacheService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\CursorPaginator;

class BookRepository
{
    public function __construct(
        private readonly BookCacheService $cacheService
    ) {}

    private array $catalogFields = [
        'books.id', 'books.isbn', 'books.title', 'books.author',
        'books.price', 'books.format', 'books.stock_quantity',
        'books.published_at', 'books.category_id', 'books.cover_image',
    ];

    public function getActiveCatalog(int $perPage = 100): CursorPaginator
    {
        return Book::select($this->catalogFields)
            ->with(['category:id,name'])
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage);
    }

    public function getCatalogByCategory(int $categoryId, int $perPage = 100): CursorPaginator
    {
        return Book::select($this->catalogFields)
            ->with(['category:id,name'])
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage);
    }

    public function findByIsbn(string $isbn): ?Book
    {
        return $this->cacheService->rememberIsbn($isbn, function () use ($isbn) {
            return Book::select(array_merge($this->catalogFields, ['books.description']))
                ->with(['category:id,name'])
                ->where('isbn', $isbn)
                ->first();
        });
    }

    public function findById(int $id): ?Book
    {
        return Book::select(array_merge($this->catalogFields, ['books.description']))
            ->with(['category:id,name,slug', 'reviews.user:id,name'])
            ->where('id', $id)
            ->first();
    }

    public function searchByFulltext(string $query, int $perPage = 50): CursorPaginator
    {
        $books = Book::select($this->catalogFields)
            ->with(['category:id,name'])
            ->where('is_active', true);

        if (DB::connection()->getDriverName() === 'sqlite') {
            return $books
                ->where(function ($builder) use ($query) {
                    $builder->where('title', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->orderBy('published_at', 'desc')
                ->orderBy('id', 'desc')
                ->cursorPaginate($perPage);
        }

        return $books
            ->whereRaw('MATCH(title, description) AGAINST(? IN BOOLEAN MODE)', [$query . '*'])
            ->orderByRaw('MATCH(title, description) AGAINST(? IN BOOLEAN MODE) DESC', [$query . '*'])
            ->cursorPaginate($perPage);
    }

    public function searchByScout(string $query, int $perPage = 50): mixed
    {
        return Book::search($query)
            ->where('is_active', true)
            ->paginate($perPage);
    }

    public function getBooksByPriceRange(float $min, float $max, int $perPage = 100): CursorPaginator
    {
        return Book::select($this->catalogFields)
            ->with(['category:id,name'])
            ->where('is_active', true)
            ->whereBetween('price', [$min, $max])
            ->orderBy('price', 'asc')
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage);
    }

    public function getExportQuery()
    {
        return Book::query()
            ->select(['isbn', 'title', 'author', 'price', 'format', 'stock_quantity', 'published_at'])
            ->where('is_active', true)
            ->orderBy('id');
    }

    public function getBestsellerStats(): Collection
    {
        return Book::selectRaw('
                category_id,
                COUNT(*) as total_books,
                AVG(price) as avg_price,
                SUM(stock_quantity) as total_inventory,
                COUNT(CASE WHEN stock_quantity > 500 THEN 1 END) as bestseller_count,
                MAX(published_at) as latest_publication
            ')
            ->where('is_active', true)
            ->groupBy('category_id')
            ->with('category:id,name')
            ->get();
    }
}
