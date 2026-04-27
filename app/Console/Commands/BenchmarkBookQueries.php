<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Lab Activity 7 — Step 11
 * Automated performance benchmarking command.
 *
 * Runs each query N times (default 100), performs a warmup pass,
 * and reports avg / min / max / total milliseconds.
 * Validates results against Lab 7 performance targets.
 * Returns non-zero exit code on failure (CI/CD integration).
 *
 * Usage:
 *   php artisan benchmark:books
 *   php artisan benchmark:books --iterations=50
 */
class BenchmarkBookQueries extends Command
{
    protected $signature   = 'benchmark:books {--iterations=100 : Number of iterations per query}';
    protected $description = 'Benchmark critical book catalog queries against Lab 7 performance targets';

    /** Performance targets from Lab 7 Section 3.2.2 (milliseconds) */
    private const TARGETS = [
        'isbn_lookup'      => 50,
        'catalog_listing'  => 100,
        'category_filter'  => 150,
        'fulltext_search'  => 300,
    ];

    public function handle(): int
    {
        $iterations = (int) $this->option('iterations');
        $this->info("PageTurner — Book Query Benchmark");
        $this->info("Iterations: {$iterations} | DB: " . DB::getDriverName());
        $this->line(str_repeat('─', 70));

        $totalBooks = DB::table('books')->count();
        $this->info("Total books in database: " . number_format($totalBooks));
        $this->newLine();

        $results = [];
        $passed  = 0;
        $failed  = 0;

        // ── 1. ISBN Lookup ────────────────────────────────────────────────────
        $sampleIsbn = DB::table('books')->value('isbn');
        if ($sampleIsbn) {
            $result = $this->benchmark('isbn_lookup', $iterations, function () use ($sampleIsbn) {
                Book::where('isbn', $sampleIsbn)->select(['id', 'isbn', 'title', 'price'])->first();
            });
            $results['ISBN Lookup (exact)'] = $result;
            $result['avg'] <= self::TARGETS['isbn_lookup'] ? $passed++ : $failed++;
        }

        // ── 2. Catalog Listing ────────────────────────────────────────────────
        $result = $this->benchmark('catalog_listing', $iterations, function () {
            Book::select(['id', 'isbn', 'title', 'author', 'price', 'stock_quantity', 'category_id'])
                ->where('is_active', true)
                ->orderBy('id', 'desc')
                ->limit(100)
                ->get();
        });
        $results['Catalog Listing (100 records)'] = $result;
        $result['avg'] <= self::TARGETS['catalog_listing'] ? $passed++ : $failed++;

        // ── 3. Category Filter ────────────────────────────────────────────────
        $categoryId = DB::table('categories')->value('id') ?? 1;
        $result = $this->benchmark('category_filter', $iterations, function () use ($categoryId) {
            Book::select(['id', 'isbn', 'title', 'price', 'stock_quantity'])
                ->where('category_id', $categoryId)
                ->where('is_active', true)
                ->orderBy('id', 'desc')
                ->limit(100)
                ->get();
        });
        $results['Category Filter'] = $result;
        $result['avg'] <= self::TARGETS['category_filter'] ? $passed++ : $failed++;

        // ── 4. Full-Text Search ───────────────────────────────────────────────
        $ftIterations = min($iterations, 50); // cap at 50 for full-text
        $driver = DB::getDriverName();
        $result = $this->benchmark('fulltext_search', $ftIterations, function () use ($driver) {
            if ($driver === 'mysql' || $driver === 'mariadb') {
                Book::whereRaw('MATCH(title, description) AGAINST(? IN BOOLEAN MODE)', ['laravel'])
                    ->select(['id', 'title', 'author', 'price'])
                    ->limit(50)
                    ->get();
            } else {
                Book::where('title', 'like', '%the%')
                    ->select(['id', 'title', 'author', 'price'])
                    ->limit(50)
                    ->get();
            }
        });
        $results['Full-Text Search'] = $result;
        $result['avg'] <= self::TARGETS['fulltext_search'] ? $passed++ : $failed++;

        // ── Results Table ─────────────────────────────────────────────────────
        $this->newLine();
        $this->line(str_repeat('─', 70));
        $this->info('RESULTS');
        $this->line(str_repeat('─', 70));

        $tableRows = [];
        foreach ($results as $name => $r) {
            $target  = self::TARGETS[$r['key']] ?? '—';
            $status  = $r['avg'] <= $target ? '✅ PASS' : '❌ FAIL';
            $tableRows[] = [
                $name,
                number_format($r['avg'], 2) . ' ms',
                number_format($r['min'], 2) . ' ms',
                number_format($r['max'], 2) . ' ms',
                "{$target} ms",
                $status,
            ];
        }

        $this->table(
            ['Query', 'Avg', 'Min', 'Max', 'Target', 'Status'],
            $tableRows
        );

        $this->newLine();
        $this->info("Passed: {$passed} / " . ($passed + $failed));

        if ($failed > 0) {
            $this->warn("{$failed} query(ies) did not meet performance targets.");
            $this->warn("Consider: adding indexes, enabling Redis caching, or switching to MySQL.");
            return self::FAILURE;
        }

        $this->info('All performance targets met! ✅');
        return self::SUCCESS;
    }

    /**
     * Run a query N times and return timing statistics.
     */
    private function benchmark(string $key, int $iterations, callable $query): array
    {
        // Warmup pass — not counted
        $query();

        $times = [];
        for ($i = 0; $i < $iterations; $i++) {
            $start   = microtime(true);
            $query();
            $times[] = (microtime(true) - $start) * 1000; // ms
        }

        return [
            'key' => $key,
            'avg' => array_sum($times) / count($times),
            'min' => min($times),
            'max' => max($times),
        ];
    }
}
