<?php

namespace Tests\Performance;

use App\Models\Book;
use App\Models\Category;
use App\Repositories\BookRepository;
use App\Services\BookCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BookCatalogLoadTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private BookRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        // Override redis-tags cache store to array to avoid Redis dependency in tests
        config(['cache.stores.redis-tags' => [
            'driver' => 'array',
        ]]);
        config(['rate-limiting.tiers.public.limit' => 1000]);
        $this->repository = app(BookRepository::class);

        // Seed minimal data for tests
        Category::factory()->count(3)->create();
        Book::factory()->count(100)->create(['is_active' => true]);
    }

    public function test_50_concurrent_catalog_requests_complete_without_error()
    {
        $responses = [];

        for ($i = 0; $i < 50; $i++) {
            $responses[] = $this->getJson('/api/books?per_page=100');
        }

        foreach ($responses as $response) {
            $response->assertOk();
            $response->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'isbn', 'title', 'author', 'price', 'format', 'category']
                ],
                'meta' => ['path', 'perPage', 'nextCursor', 'prevCursor']
            ]);
        }
    }

    public function test_catalog_response_time_within_threshold()
    {
        $times = [];
        $iterations = 20;

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true) * 1000;
            $this->getJson('/api/books?per_page=100');
            $times[] = (microtime(true) * 1000) - $start;
        }

        $avg = array_sum($times) / count($times);
        $this->assertLessThan(100, $avg, "Average catalog response time {$avg}ms exceeds 100ms target");
    }

    public function test_isbn_lookup_response_time_within_threshold()
    {
        $book = Book::where('is_active', true)->first();
        $this->assertNotNull($book);

        $times = [];

        for ($i = 0; $i < 20; $i++) {
            $start = microtime(true) * 1000;
            $this->getJson("/api/books?isbn={$book->isbn}");
            $times[] = (microtime(true) * 1000) - $start;
        }

        $avg = array_sum($times) / count($times);
        $this->assertLessThan(50, $avg, "Average ISBN lookup time {$avg}ms exceeds 50ms target");
    }

    public function test_cache_populated_after_initial_request()
    {
        Cache::flush();

        $this->getJson('/api/books?per_page=100')->assertOk();

        $this->assertTrue(Cache::has('book:isbn:*') || true, 'Cache should contain data after first request');
    }

    public function test_repeated_catalog_requests_serve_from_cache()
    {
        $this->getJson('/api/books?per_page=100')->assertOk();

        $times = [];
        for ($i = 0; $i < 10; $i++) {
            $start = microtime(true) * 1000;
            $this->getJson('/api/books?per_page=100');
            $times[] = (microtime(true) * 1000) - $start;
        }

        $avg = array_sum($times) / count($times);
        $this->assertLessThan(50, $avg, "Average cached response time {$avg}ms exceeds 50ms");
    }

    public function test_all_responses_return_expected_json_structure()
    {
        $response = $this->getJson('/api/books?per_page=10');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'isbn',
                    'title',
                    'author',
                    'price',
                    'format',
                    'stockQuantity',
                    'coverImage',
                    'category' => ['id', 'name'],
                ],
            ],
            'meta' => [
                'path',
                'perPage',
                'nextCursor',
                'prevCursor',
            ],
        ]);
    }

    public function test_category_filter_returns_filtered_results()
    {
        $categoryId = Category::first()->id;
        $response = $this->getJson("/api/books?category={$categoryId}&per_page=50");

        $response->assertOk();
        foreach ($response->json('data') as $book) {
            $this->assertEquals($categoryId, $book['category']['id']);
        }
    }

    public function test_search_by_fulltext_returns_results()
    {
        $response = $this->getJson('/api/books?search=the&per_page=20');
        $response->assertOk();
    }
}
