<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class BookRecommendationService
{
    /**
     * Get recommended books for a specific book (similar books)
     *
     * @param Book $book
     * @param int $limit
     * @return Collection
     */
    public function getSimilarBooks(Book $book, int $limit = 5): Collection
    {
        return Book::where('id', '!=', $book->id)
            ->where('category_id', $book->category_id)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recommended books based on user's purchase history
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getRecommendedForUser(User $user, int $limit = 5): Collection
    {
        // Get categories from user's purchased books
        $purchasedCategories = $user->orders()
            ->with('items.book.category')
            ->get()
            ->pluck('items.*.book.category_id')
            ->flatten()
            ->unique()
            ->values();

        if ($purchasedCategories->isEmpty()) {
            // If no purchases, return popular books
            return $this->getPopularBooks($limit);
        }

        // Get books from similar categories that user hasn't purchased
        $purchasedBookIds = $user->orders()
            ->with('items.book')
            ->get()
            ->pluck('items.*.book.id')
            ->flatten()
            ->unique()
            ->values();

        return Book::whereIn('category_id', $purchasedCategories)
            ->whereNotIn('id', $purchasedBookIds)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get popular books based on reviews and ratings
     *
     * @param int $limit
     * @return Collection
     */
    public function getPopularBooks(int $limit = 5): Collection
    {
        return Book::with('reviews')
            ->where('is_active', true)
            ->get()
            ->sortByDesc(function ($book) {
                return $book->reviews->avg('rating') ?? 0;
            })
            ->take($limit);
    }

    /**
     * Get trending books (recently added with good ratings)
     *
     * @param int $limit
     * @return Collection
     */
    public function getTrendingBooks(int $limit = 5): Collection
    {
        return Book::with('reviews')
            ->where('is_active', true)
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->sortByDesc(function ($book) {
                $rating = $book->reviews->avg('rating') ?? 0;
                $reviewCount = $book->reviews->count();
                // Score based on rating and review count
                return ($rating * 0.7) + (min($reviewCount, 10) * 0.3);
            })
            ->take($limit);
    }

    /**
     * Get books by the same author
     *
     * @param Book $book
     * @param int $limit
     * @return Collection
     */
    public function getBooksByAuthor(Book $book, int $limit = 5): Collection
    {
        return Book::where('author', $book->author)
            ->where('id', '!=', $book->id)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
