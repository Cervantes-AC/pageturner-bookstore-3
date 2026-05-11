<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE books REMOVE PARTITIONING');

            DB::statement('
                ALTER TABLE books
                PARTITION BY RANGE (YEAR(published_at)) (
                    PARTITION p_old VALUES LESS THAN (2000),
                    PARTITION p2000 VALUES LESS THAN (2005),
                    PARTITION p2005 VALUES LESS THAN (2010),
                    PARTITION p2010 VALUES LESS THAN (2015),
                    PARTITION p2015 VALUES LESS THAN (2020),
                    PARTITION p2020 VALUES LESS THAN (2025),
                    PARTITION p_future VALUES LESS THAN MAXVALUE
                )
            ');

            Log::info('Books table partitioned by publication year (MySQL only)');
        } else {
            Log::info('Partitioning skipped — requires MySQL 8.0+');
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE books REMOVE PARTITIONING');
        }
    }
};
