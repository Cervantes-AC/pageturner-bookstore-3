<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->index('isbn');
            $table->index('price');
            $table->index('stock_quantity');
            $table->index('category_id');
            $table->index('created_at');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('book_id');
            $table->index('order_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['book_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['isbn']);
            $table->dropIndex(['price']);
            $table->dropIndex(['stock_quantity']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['book_id']);
            $table->dropIndex(['order_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['book_id', 'user_id']);
        });
    }
};
