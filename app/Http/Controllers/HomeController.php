<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Review;
use App\Models\User;
use App\Services\BookRecommendationService;

class HomeController extends Controller
{
    public function index(BookRecommendationService $recommendationService)
    {
        $featuredBooks = Book::with('category')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $heroBook = Book::whereNotNull('cover_image')->latest()->first() ?? $featuredBooks->first();
        $categories = Category::withCount('books')->orderByDesc('books_count')->get();
        $trendingBooks = $recommendationService->getTrendingBooks(8);
        $stats = [
            'books' => Book::count(),
            'categories' => Category::count(),
            'readers' => User::count(),
            'reviews' => Review::count(),
        ];

        return view('home', compact('featuredBooks', 'heroBook', 'categories', 'stats', 'trendingBooks'));
    }
}
