<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Lab Activity 7 — Step 1
 * Adds Lab 7 columns to the books table and creates covering/composite indexes
 * for high-volume catalog queries on 1M+ records.
 *
 * New columns:
 *   - publisher      : publisher name (replaces cover_image for mass data)
 *   - format         : ebook | paperback | hardcover | audiobook
 *   - published_at   : full date (replaces publication_year for range partitioning)
 *   - is_active      : soft-visibility flag (85% of seeded books are active)
 *
 * Indexes added:
 *   - idx_books_catalog_filter  : composite (category_id, published_at, is_active)
 *   - idx_books_price_stock     : covering  (price, stock_quantity, id)
 *   - idx_books_active          : single    (is_active)
 *   - idx_books_isbn_lookup     : single    (isbn) — already unique, explicit for clarity
 *
 * Note: fullText index is MySQL-only. On SQLite (dev) it is skipped gracefully.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // New columns for Lab 7
            $table->string('publisher')->nullable()->after('author');
            $table->enum('format', ['ebook', 'paperback', 'hardcover', 'audiobook'])
                  ->default('paperback')->after('publisher');
            $table->date('published_at')->nullable()->after('format');
            $table->boolean('is_active')->default(true)->after('is_featured');
        });

        // Composite index for common catalog filtering
        Schema::table('books', function (Blueprint $table) {
            $table->index(['category_id', 'published_at', 'is_active'], 'idx_books_catalog_filter');
            $table->index(['price', 'stock_quantity', 'id'],            'idx_books_price_stock');
            $table->index('is_active',                                   'idx_books_active');
        });

        // Full-text index — MySQL/MariaDB only (skip on SQLite)
        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE books ADD FULLTEXT idx_books_fulltext (title, description)');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE books DROP INDEX idx_books_fulltext');
        }

        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex('idx_books_catalog_filter');
            $table->dropIndex('idx_books_price_stock');
            $table->dropIndex('idx_books_active');
            $table->dropColumn(['publisher', 'format', 'published_at', 'is_active']);
        });
    }
};
