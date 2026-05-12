<?php

namespace App\Jobs;

use App\Models\Book;
use App\Services\BookCacheService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WarmCategoryCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $categoryId
    ) {}

    public function handle(BookCacheService $cacheService): void
    {
        $books = Book::select(['id', 'title', 'author', 'price', 'format', 'stock_quantity', 'cover_image'])
            ->where('category_id', $this->categoryId)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->limit(1000)
            ->get();

        $cacheService->warmCategoryPopular($this->categoryId, $books->toArray());
    }
}
