<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Composite index for export queries filtering by is_active and created_at
            $table->index(['is_active', 'created_at', 'id'], 'idx_books_export_filter');
            
            // Composite index for category + is_active filtering
            $table->index(['category_id', 'is_active'], 'idx_books_category_active');
            
            // Composite index for price range queries
            $table->index(['is_active', 'price', 'id'], 'idx_books_price_filter');
            
            // Composite index for stock status queries
            $table->index(['is_active', 'stock_quantity', 'id'], 'idx_books_stock_filter');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex('idx_books_export_filter');
            $table->dropIndex('idx_books_category_active');
            $table->dropIndex('idx_books_price_filter');
            $table->dropIndex('idx_books_stock_filter');
        });
    }
};
