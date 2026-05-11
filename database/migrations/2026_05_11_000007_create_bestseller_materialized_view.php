<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            $viewSql = "
                CREATE OR REPLACE TABLE mv_bestseller_stats AS
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
            ";

            DB::statement("DROP TABLE IF EXISTS mv_bestseller_stats");
            DB::statement($viewSql);

            DB::statement("
                CREATE INDEX idx_mv_bestseller_category
                ON mv_bestseller_stats (category_id)
            ");

            Log::info('Materialized view mv_bestseller_stats created');
        } else {
            Log::info('Materialized view skipped — requires MySQL');
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("DROP TABLE IF EXISTS mv_bestseller_stats");
        }
    }
};
