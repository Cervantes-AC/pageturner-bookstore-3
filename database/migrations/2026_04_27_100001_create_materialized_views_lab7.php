<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Lab Activity 7 — Step 8
 * Creates a materialized-view equivalent table: mv_bestseller_stats.
 *
 * True materialized views are MySQL 8.0+ / PostgreSQL features.
 * On SQLite (dev) we create a regular table with the same schema and
 * populate it via the app:refresh-materialized-views Artisan command.
 *
 * Refresh strategy: scheduled hourly via routes/console.php.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Materialized view table — pre-computed bestseller stats per category
        Schema::create('mv_bestseller_stats', function ($table) {
            $table->unsignedBigInteger('category_id')->primary();
            $table->unsignedBigInteger('total_books')->default(0);
            $table->decimal('avg_price', 10, 2)->default(0);
            $table->unsignedBigInteger('total_inventory')->default(0);
            $table->unsignedBigInteger('bestseller_count')->default(0);
            $table->date('latest_publication')->nullable();
            $table->timestamp('refreshed_at')->nullable();
        });

        // Query performance log table for slow-query monitoring
        Schema::create('query_performance_logs', function ($table) {
            $table->id();
            $table->string('query_type', 100);
            $table->decimal('duration_ms', 10, 3);
            $table->unsignedBigInteger('result_count')->default(0);
            $table->json('context')->nullable();
            $table->timestamps();
            $table->index(['query_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('query_performance_logs');
        Schema::dropIfExists('mv_bestseller_stats');
    }
};
