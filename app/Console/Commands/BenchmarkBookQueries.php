<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Repositories\BookRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BenchmarkBookQueries extends Command
{
    protected $signature = 'benchmark:books
                            {--iterations=100 : Number of iterations per query}
                            {--warmup=5 : Number of warmup iterations}';

    protected $description = 'Benchmark book query performance against defined targets';

    private array $targets = [
        'isbn_lookup' => 50,
        'catalog_listing' => 100,
        'category_filter' => 150,
        'fulltext_search' => 300,
    ];

    public function handle(BookRepository $repository): int
    {
        $iterations = (int)$this->option('iterations');
        $warmup = (int)$this->option('warmup');

        $this->info('Performance Benchmark: Book Queries');
        $this->warn("Targets: " . json_encode($this->targets) . " ms\n");

        $benchmarks = [
            'isbn_lookup' => fn() => $this->benchmarkIsbnLookup($repository),
            'catalog_listing' => fn() => $this->benchmarkCatalogListing($repository),
            'category_filter' => fn() => $this->benchmarkCategoryFilter($repository),
            'fulltext_search' => fn() => $this->benchmarkFulltextSearch($repository),
        ];

        $allPassed = true;

        foreach ($benchmarks as $name => $benchmark) {
            $this->info("Benchmarking: {$name}");

            for ($i = 0; $i < $warmup; $i++) {
                $benchmark();
            }

            $times = [];
            for ($i = 0; $i < $iterations; $i++) {
                $start = microtime(true) * 1000;
                $benchmark();
                $times[] = (microtime(true) * 1000) - $start;
            }

            $avg = array_sum($times) / count($times);
            $min = min($times);
            $max = max($times);
            $total = array_sum($times);
            $target = $this->targets[$name];
            $passed = $avg <= $target;

            $status = $passed ? 'PASS' : 'FAIL';
            $icon = $passed ? "\xF0\x9F\x9F\xA2" : "\xF0\x9F\x94\xB4";

            $this->table(
                ['Metric', 'Value', 'Target', 'Status'],
                [
                    ['Average', round($avg, 2) . ' ms', $target . ' ms', $status],
                    ['Min', round($min, 2) . ' ms', '-', '-'],
                    ['Max', round($max, 2) . ' ms', '-', '-'],
                    ['Total (' . $iterations . ' iters)', round($total, 2) . ' ms', '-', '-'],
                ]
            );

            $this->newLine();

            if (!$passed) {
                $allPassed = false;
            }
        }

        if ($allPassed) {
            $this->info("\xE2\x9C\x85 All benchmarks passed!");
            return 0;
        }

        $this->error("\xE2\x9D\x8C Some benchmarks did not meet targets!");
        return 1;
    }

    private function benchmarkIsbnLookup(BookRepository $repository): void
    {
        $isbn = Book::where('is_active', true)->inRandomOrder()->value('isbn');
        if ($isbn) {
            $repository->findByIsbn($isbn);
        }
    }

    private function benchmarkCatalogListing(BookRepository $repository): void
    {
        $repository->getActiveCatalog(100);
    }

    private function benchmarkCategoryFilter(BookRepository $repository): void
    {
        $categoryId = Book::where('is_active', true)
            ->select('category_id')
            ->distinct()
            ->inRandomOrder()
            ->value('category_id');

        if ($categoryId) {
            $repository->getCatalogByCategory($categoryId, 100);
        }
    }

    private function benchmarkFulltextSearch(BookRepository $repository): void
    {
        $terms = ['the', 'of', 'and', 'in', 'book', 'story', 'world', 'life'];
        $term = $terms[array_rand($terms)];
        $repository->searchByFulltext($term, 50);
    }
}
