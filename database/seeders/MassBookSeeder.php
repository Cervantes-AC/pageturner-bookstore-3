<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Lab Activity 7 — Step 3
 * Seeds 1,000,000 book records using chunked raw batch inserts.
 *
 * CRITICAL: Never use Book::factory()->count(1000000)->create()
 * That instantiates 1M Eloquent models simultaneously → OOM crash.
 *
 * This seeder uses:
 *  - factory()->make()  → generates models WITHOUT persisting (no DB round-trip per record)
 *  - ->toArray()        → converts to plain arrays
 *  - DB::table()->insert() → single raw INSERT per chunk (no Eloquent overhead)
 *  - gc_collect_cycles()   → forces PHP garbage collection every 10 chunks
 *
 * Memory target : < 512 MB
 * Time target   : < 10 minutes on standard hardware
 */
class MassBookSeeder extends Seeder
{
    private const CHUNK_SIZE    = 5000;   // Optimal batch size for MySQL/SQLite
    private const TOTAL_RECORDS = 1_000_000;

    public function run(): void
    {
        $this->command->info('Starting MassBookSeeder — target: ' . number_format(self::TOTAL_RECORDS) . ' records');
        $this->command->info('Chunk size: ' . number_format(self::CHUNK_SIZE));

        $startTime = microtime(true);
        $inserted  = 0;
        $bar       = $this->command->getOutput()->createProgressBar(self::TOTAL_RECORDS / self::CHUNK_SIZE);
        $bar->start();

        // Disable query logging to save memory during mass insert
        DB::disableQueryLog();

        while ($inserted < self::TOTAL_RECORDS) {
            $batchSize = min(self::CHUNK_SIZE, self::TOTAL_RECORDS - $inserted);

            // make() generates models WITHOUT saving — no Eloquent overhead
            // withoutCasting() ensures dates stay as plain strings for raw insert
            $books = Book::factory()->count($batchSize)->make()
                ->map(fn($b) => array_merge($b->getAttributes(), [
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ]))
                ->toArray();

            // Raw batch insert — bypasses Eloquent events and model hydration
            DB::table('books')->insert($books);

            $inserted += $batchSize;
            $bar->advance();

            // Force garbage collection every 10 chunks to keep memory bounded
            if ($inserted % (self::CHUNK_SIZE * 10) === 0) {
                unset($books);
                gc_collect_cycles();

                $elapsed = round(microtime(true) - $startTime, 1);
                $rate    = round($inserted / max($elapsed, 1));
                $this->command->getOutput()->writeln(
                    "\n  → {$inserted} / " . self::TOTAL_RECORDS .
                    " inserted | {$elapsed}s elapsed | {$rate} rec/s | " .
                    round(memory_get_usage(true) / 1024 / 1024, 1) . ' MB RAM'
                );
            }
        }

        $bar->finish();

        $elapsed = round(microtime(true) - $startTime, 1);
        $peak    = round(memory_get_peak_usage(true) / 1024 / 1024, 1);

        $this->command->newLine(2);
        $this->command->info("✅ Seeded " . number_format($inserted) . " books in {$elapsed}s");
        $this->command->info("   Peak memory: {$peak} MB");
        $this->command->info("   Rate: " . number_format(round($inserted / max($elapsed, 1))) . " records/second");

        if ($peak > 512) {
            $this->command->warn("⚠️  Peak memory ({$peak} MB) exceeded 512 MB target.");
        }
        if ($elapsed > 600) {
            $this->command->warn("⚠️  Seeding took {$elapsed}s — exceeded 10-minute target.");
        }
    }
}
