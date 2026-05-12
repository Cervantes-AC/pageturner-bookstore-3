<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MassBookSeeder extends Seeder
{
    private const CHUNK_SIZE = 5000;
    private const TOTAL_RECORDS = 1000000;

    public function run(): void
    {
        $this->command?->info('Starting mass seeding of ' . number_format(self::TOTAL_RECORDS) . ' books...');
        $this->command?->warn('Memory limit: 512 MB | Target: < 10 minutes');
        $this->command?->newLine();

        $inserted = 0;
        $startTime = microtime(true);
        $lastProgress = 0;

        while ($inserted < self::TOTAL_RECORDS) {
            $batchSize = min(self::CHUNK_SIZE, self::TOTAL_RECORDS - $inserted);

            $books = Book::factory()->count($batchSize)->make()->toArray();

            DB::table('books')->insert($books);

            $inserted += $batchSize;

            if ($inserted % (self::CHUNK_SIZE * 10) === 0) {
                unset($books);
                gc_collect_cycles();
            }

            $elapsed = microtime(true) - $startTime;
            $rate = $inserted / max($elapsed, 0.001);
            $progress = ($inserted / self::TOTAL_RECORDS) * 100;

            if (floor($progress / 10) > $lastProgress) {
                $lastProgress = (int)floor($progress / 10);
                $memory = round(memory_get_peak_usage(true) / 1024 / 1024, 1);
                $this->command?->info(sprintf(
                    '[%d%%] %s records inserted | %s/sec | %s MB peak | %s elapsed',
                    (int)$progress,
                    number_format($inserted),
                    number_format($rate, 0),
                    $memory,
                    gmdate('H:i:s', (int)$elapsed)
                ));
            }
        }

        $totalTime = microtime(true) - $startTime;
        $peakMemory = round(memory_get_peak_usage(true) / 1024 / 1024, 1);

        $this->command?->newLine();
        $this->command?->info('Mass seeding completed!');
        $this->command?->info('Total records: ' . number_format($inserted));
        $this->command?->info('Total time: ' . gmdate('H:i:s', (int)$totalTime));
        $this->command?->info('Peak memory: ' . $peakMemory . ' MB');
        $this->command?->info('Average rate: ' . number_format($inserted / $totalTime, 0) . ' records/sec');
    }
}
