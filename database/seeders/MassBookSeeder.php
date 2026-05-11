<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MassBookSeeder extends Seeder
{
    private const TOTAL_RECORDS = 1000000;

    public function run(): void
    {
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $chunkSize = $isSqlite ? 50 : 5000;

        $this->command->info('Starting 1M book seeding...');
        $this->command->info('Driver: ' . DB::connection()->getDriverName() . " (chunk size: {$chunkSize})");
        $start = microtime(true);
        $inserted = 0;
        $memoryBefore = memory_get_usage(true);
        $columnCount = 15;

        while ($inserted < self::TOTAL_RECORDS) {
            $batchSize = min($chunkSize, self::TOTAL_RECORDS - $inserted);

            DB::beginTransaction();
            try {
                $books = Book::factory()->count($batchSize)->make()->toArray();
                DB::table('books')->insert($books);
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->command->error("Batch failed at {$inserted}: " . $e->getMessage());
                throw $e;
            }

            $inserted += $batchSize;

            if ($inserted % ($chunkSize * 10) === 0 || $inserted === self::TOTAL_RECORDS) {
                unset($books);
                gc_collect_cycles();
                $elapsed = round(microtime(true) - $start, 2);
                $memory = round((memory_get_usage(true) - $memoryBefore) / 1024 / 1024, 2);
                $rate = round($inserted / $elapsed, 0);
                $this->command->info("  {$inserted}/" . self::TOTAL_RECORDS . " records ({$elapsed}s, {$memory}MB, {$rate} rec/s)");
            }
        }

        $this->command->info('✓ 1 million books seeded in ' . round(microtime(true) - $start, 2) . ' seconds');
    }
}
