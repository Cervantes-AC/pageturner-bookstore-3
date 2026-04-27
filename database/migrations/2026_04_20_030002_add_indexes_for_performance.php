<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->index('category_id', 'idx_books_category');
            $table->index('price', 'idx_books_price');
            $table->index('is_featured', 'idx_books_featured');
            $table->index('created_at', 'idx_books_created');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id', 'idx_orders_user');
            $table->index('status', 'idx_orders_status');
            $table->index('created_at', 'idx_orders_created');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('book_id', 'idx_reviews_book');
            $table->index('user_id', 'idx_reviews_user');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex('idx_books_category');
            $table->dropIndex('idx_books_price');
            $table->dropIndex('idx_books_featured');
            $table->dropIndex('idx_books_created');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_user');
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_created');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('idx_reviews_book');
            $table->dropIndex('idx_reviews_user');
        });
    }
};
