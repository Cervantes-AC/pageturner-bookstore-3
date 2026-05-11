<?php

namespace Tests\Performance;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookCatalogLoadTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private string $isbn;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = Category::factory()->create(['name' => 'Test Category']);
        $this->isbn = '978-0-0000-0000-0';
        Book::factory()->count(50)->create([
            'category_id' => $this->category->id,
            'is_active' => true,
        ]);
    }

    public function test_50_concurrent_catalog_requests_complete_without_error(): void
    {
        $responses = [];
        $start = microtime(true);

        for ($i = 0; $i < 50; $i++) {
            $responses[] = $this->getJson('/api/books?per_page=20');
        }

        $totalTime = (microtime(true) - $start) * 1000;

        foreach ($responses as $response) {
            $response->assertOk();
            $response->assertJsonStructure([
                'data' => [['id', 'title', 'isbn', 'price']],
                'meta' => ['per_page', 'next_cursor', 'has_more'],
            ]);
        }

        $this->assertLessThan(10000, $totalTime, '50 concurrent requests should complete in under 10 seconds');
    }

    public function test_isbn_lookup_under_50ms(): void
    {
        $book = Book::first();
        $times = [];

        for ($i = 0; $i < 10; $i++) {
            $start = microtime(true);
            $this->getJson("/api/books/{$book->id}");
            $times[] = (microtime(true) - $start) * 1000;
        }

        $avg = array_sum($times) / count($times);
        $this->assertLessThan(50, $avg, "Average ISBN lookup time ({$avg}ms) exceeds 50ms target");
    }

    public function test_cache_populated_after_initial_request(): void
    {
        Cache::shouldReceive('tags')
            ->atLeast()->once()
            ->andReturnSelf();

        $this->getJson('/api/books?per_page=20');
    }

    public function test_all_responses_return_expected_json_structure(): void
    {
        $response = $this->getJson('/api/books?per_page=20');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [[
                'id',
                'isbn',
                'title',
                'author',
                'price',
                'stock_quantity',
                'format',
                'cover_image_url',
                'is_featured',
                'published_at',
                'category' => ['id', 'name'],
            ]],
            'meta' => ['per_page', 'next_cursor', 'has_more'],
        ]);
    }

    public function test_no_n_plus_one_queries(): void
    {
        Book::factory()->count(10)->create(['category_id' => $this->category->id]);

        DB::enableQueryLog();
        $response = $this->getJson('/api/books?per_page=100');
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $response->assertOk();
        $selectQueries = array_filter($queries, fn($q) => str_starts_with($q['query'], 'select'));
        $this->assertLessThanOrEqual(2, count($selectQueries), 'N+1 detected: too many SELECT queries');
    }

    protected function tearDown(): void
    {
        Cache::clear();
        parent::tearDown();
    }
}
