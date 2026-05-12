<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshMaterializedViews extends Command
{
    protected $signature = 'app:refresh-materialized-views';

    protected $description = 'Refresh materialized view tables for reporting';

    public function handle(): int
    {
        $this->info('Refreshing bestseller stats...');

        DB::table('mv_bestseller_stats')->truncate();

        $stats = Book::selectRaw('
                category_id,
                COUNT(*) as total_books,
                AVG(price) as avg_price,
                SUM(stock_quantity) as total_inventory,
                COUNT(CASE WHEN stock_quantity > 500 THEN 1 END) as bestseller_count,
                MAX(publication_year) as latest_publication
            ')
            ->where('is_active', true)
            ->groupBy('category_id')
            ->get();

        $inserted = 0;
        foreach ($stats as $stat) {
            DB::table('mv_bestseller_stats')->insert([
                'category_id' => $stat->category_id,
                'total_books' => $stat->total_books,
                'avg_price' => round($stat->avg_price, 2),
                'total_inventory' => $stat->total_inventory,
                'bestseller_count' => $stat->bestseller_count,
                'latest_publication' => $stat->latest_publication,
                'refreshed_at' => now(),
            ]);
            $inserted++;
        }

        $this->info("Refreshed {$inserted} category statistics.");
        return 0;
    }
}
