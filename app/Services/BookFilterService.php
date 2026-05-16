<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;

class BookFilterService
{
    /**
     * Apply filters to book query
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    public function applyFilters(Builder $query, array $filters): Builder
    {
        // Category filter
        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        // Search filter (title, author, ISBN)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // Year filter
        if (!empty($filters['year'])) {
            $query->where('publication_year', $filters['year']);
        }

        // Price range filter
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Stock status filter
        if (!empty($filters['stock_status'])) {
            if ($filters['stock_status'] === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($filters['stock_status'] === 'out_of_stock') {
                $query->where('stock_quantity', '=', 0);
            }
        }

        // Active status filter
        if (!isset($filters['include_inactive']) || !$filters['include_inactive']) {
            $query->where('is_active', true);
        }

        return $query;
    }

    /**
     * Apply sorting to book query
     *
     * @param Builder $query
     * @param string|null $sort
     * @return Builder
     */
    public function applySorting(Builder $query, ?string $sort = null): Builder
    {
        if (!$sort) {
            return $query->orderBy('created_at', 'desc');
        }

        return match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'title_asc' => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            'rating' => $query->orderBy('average_rating', 'desc'),
            'bestseller' => $query->leftJoin('order_items', 'books.id', '=', 'order_items.book_id')
                ->selectRaw('books.*, SUM(order_items.quantity) as total_sold')
                ->groupBy('books.id')
                ->orderByDesc('total_sold'),
            default => $query->orderBy('created_at', 'desc'),
        };
    }

    /**
     * Get available filter options
     *
     * @return array
     */
    public function getFilterOptions(): array
    {
        return [
            'years' => Book::whereNotNull('publication_year')
                ->distinct()
                ->orderBy('publication_year', 'desc')
                ->pluck('publication_year'),
            'formats' => Book::distinct()
                ->whereNotNull('format')
                ->pluck('format'),
            'price_ranges' => [
                ['min' => 0, 'max' => 10, 'label' => 'Under $10'],
                ['min' => 10, 'max' => 20, 'label' => '$10 - $20'],
                ['min' => 20, 'max' => 50, 'label' => '$20 - $50'],
                ['min' => 50, 'max' => 999999, 'label' => 'Over $50'],
            ],
        ];
    }
}
