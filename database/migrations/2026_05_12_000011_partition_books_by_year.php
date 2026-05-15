<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::create('book_yearly_partitions', function ($table) {
            $table->unsignedBigInteger('book_id');
            $table->string('title');
            $table->string('author');
            $table->string('isbn');
            $table->decimal('price', 10, 2);
            $table->year('partition_year');
            $table->timestamps();
            
            $table->primary(['book_id', 'partition_year']);
        });

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE book_yearly_partitions PARTITION BY RANGE (partition_year) (
                PARTITION p_old VALUES LESS THAN (2000),
                PARTITION p2000 VALUES LESS THAN (2005),
                PARTITION p2005 VALUES LESS THAN (2010),
                PARTITION p2010 VALUES LESS THAN (2015),
                PARTITION p2015 VALUES LESS THAN (2020),
                PARTITION p2020 VALUES LESS THAN (2025),
                PARTITION p_future VALUES LESS THAN MAXVALUE
            )');
        }

        if ($driver === 'mysql') {
            DB::statement("
                INSERT INTO book_yearly_partitions (book_id, title, author, isbn, price, partition_year, created_at, updated_at)
                SELECT id, title, author, isbn, price, COALESCE(YEAR(published_at), 0), NOW(), NOW()
                FROM books
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('book_yearly_partitions');
    }
};
