<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Lab Activity 7 — Step 8
 * Refreshes the mv_bestseller_stats materialized view table.
 *
 * On MySQL: could use a true materialized view or stored procedure.
 * Here we use a portable REPLACE INTO approach that works on SQLite too.
 *
 * Scheduled hourly in routes/console.php.
 */
class RefreshMaterializedViews extends Command
{
    protected $signature   = 'app:refresh-materialized-views';
    protected $description = 'Refresh mv_bestseller_stats materialized view table';

    public function handle(): int
    {
        $start = microtime(true);

        $stats = DB::table('books')
            ->select([
                'category_id',
                DB::raw('COUNT(*) as total_books'),
                DB::raw('AVG(price) as avg_price'),
                DB::raw('SUM(stock_quantity) as total_inventory'),
                DB::raw('COUNT(CASE WHEN stock_quantity > 500 THEN 1 END) as bestseller_count'),
                DB::raw('MAX(published_at) as latest_publication'),
            ])
            ->where('is_active', true)
            ->groupBy('category_id')
            ->get();

        $now = now();

        DB::table('mv_bestseller_stats')->truncate();

        $rows = $stats->map(fn($row) => [
            'category_id'       => $row->category_id,
            'total_books'       => $row->total_books,
            'avg_price'         => round($row->avg_price, 2),
            'total_inventory'   => $row->total_inventory,
            'bestseller_count'  => $row->bestseller_count,
            'latest_publication'=> $row->latest_publication,
            'refreshed_at'      => $now,
        ])->toArray();

        if (!empty($rows)) {
            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('mv_bestseller_stats')->insert($chunk);
            }
        }

        $elapsed = round((microtime(true) - $start) * 1000, 1);
        $count   = count($rows);

        Log::info("app:refresh-materialized-views: refreshed {$count} category stats in {$elapsed}ms");
        $this->info("Refreshed {$count} category stats in {$elapsed}ms");

        return self::SUCCESS;
    }
}
