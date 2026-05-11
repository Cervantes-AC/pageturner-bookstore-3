<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefreshMaterializedViews extends Command
{
    protected $signature = 'app:refresh-materialized-views';
    protected $description = 'Refresh materialized views for reporting';

    public function handle(): int
    {
        $this->info('Refreshing materialized views...');

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("DROP TABLE IF EXISTS mv_bestseller_stats");
            DB::statement("
                CREATE TABLE mv_bestseller_stats AS
                SELECT
                    category_id,
                    COUNT(*) as total_books,
                    AVG(price) as avg_price,
                    SUM(stock_quantity) as total_inventory,
                    COUNT(CASE WHEN stock_quantity > 500 THEN 1 END) as bestseller_count,
                    MAX(published_at) as latest_publication
                FROM books
                WHERE is_active = true
                GROUP BY category_id
            ");
            DB::statement("
                CREATE INDEX idx_mv_bestseller_category
                ON mv_bestseller_stats (category_id)
            ");
            $this->info('Materialized view mv_bestseller_stats refreshed.');
            Log::info('Materialized view mv_bestseller_stats refreshed successfully.');
        } else {
            $this->warn('Materialized views require MySQL — skipped.');
        }

        return 0;
    }
}
