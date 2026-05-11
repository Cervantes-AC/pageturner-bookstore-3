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
                            {--warmup=5 : Warmup iterations before measuring}';

    protected $description = 'Benchmark critical book queries against performance targets';

    private const TARGETS = [
        'isbn_lookup' => ['label' => 'ISBN Lookup (exact)', 'max_ms' => 50],
        'catalog_listing' => ['label' => 'Catalog Listing (100 rec)', 'max_ms' => 100],
        'category_filter' => ['label' => 'Category Filter (100K+)', 'max_ms' => 150],
        'fulltext_search' => ['label' => 'Full-Text Search (1M rec)', 'max_ms' => 300],
    ];

    public function handle(BookRepository $repository): int
    {
        $iterations = (int) $this->option('iterations');
        $warmup = (int) $this->option('warmup');
        $results = [];
        $failed = false;

        $this->newLine();
        $this->info('Book Query Performance Benchmark');
        $this->line('Target: ' . config('app.env') . ' environment');
        $this->line("Iterations: {$iterations} (warmup: {$warmup})");
        $this->newLine();

        $book = Book::where('is_active', true)->inRandomOrder()->first();
        $isbn = $book?->isbn ?? '978-0-0000-0000-0';
        $categoryId = Book::where('is_active', true)->value('category_id') ?? 1;

        $queries = [
            'isbn_lookup' => fn() => $repository->findByIsbn($isbn),
            'catalog_listing' => fn() => $repository->getActiveCatalog(100),
            'category_filter' => fn() => $repository->findByCategory($categoryId, 100),
            'fulltext_search' => fn() => Book::search('programming')->get(),
        ];

        foreach ($queries as $key => $query) {
            $this->line("Benchmarking: " . self::TARGETS[$key]['label']);

            for ($i = 0; $i < $warmup; $i++) {
                $query();
            }

            $times = [];
            for ($i = 0; $i < $iterations; $i++) {
                $start = microtime(true);
                $query();
                $times[] = (microtime(true) - $start) * 1000;
            }

            $avg = array_sum($times) / count($times);
            $min = min($times);
            $max = max($times);
            $total = array_sum($times);
            $target = self::TARGETS[$key]['max_ms'];
            $passed = $avg <= $target;

            $results[$key] = compact('avg', 'min', 'max', 'total', 'passed', 'target');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Average', round($avg, 2) . ' ms'],
                    ['Min', round($min, 2) . ' ms'],
                    ['Max', round($max, 2) . ' ms'],
                    ['Total', round($total, 2) . ' ms'],
                    ['Target', "≤ {$target} ms"],
                    ['Status', $passed ? '<fg=green>PASS</>' : '<fg=red>FAIL</>'],
                ]
            );

            if (!$passed) {
                $failed = true;
            }
        }

        $this->newLine();
        if ($failed) {
            $this->error('Some benchmarks did not meet performance targets.');
            return 1;
        }

        $this->info('All benchmarks passed performance targets.');
        return 0;
    }
}
