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
            // Safely drop indexes if they exist
            try { $table->dropIndex(['isbn']); } catch (\Exception $e) {}
            try { $table->dropIndex(['price']); } catch (\Exception $e) {}
            try { $table->dropIndex(['stock_quantity']); } catch (\Exception $e) {}
            try { $table->dropIndex(['created_at']); } catch (\Exception $e) {}
        });

        Schema::table('orders', function (Blueprint $table) {
            try { $table->dropIndex(['user_id']); } catch (\Exception $e) {}
            try { $table->dropIndex(['status']); } catch (\Exception $e) {}
            try { $table->dropIndex(['created_at']); } catch (\Exception $e) {}
        });

        Schema::table('order_items', function (Blueprint $table) {
            try { $table->dropIndex(['book_id']); } catch (\Exception $e) {}
            try { $table->dropIndex(['order_id']); } catch (\Exception $e) {}
        });

        Schema::table('reviews', function (Blueprint $table) {
            try { $table->dropIndex(['book_id', 'user_id']); } catch (\Exception $e) {}
        });
    }
};
