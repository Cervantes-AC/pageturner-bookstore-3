<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('publisher', 255)->nullable()->after('author');
            $table->string('format', 50)->nullable()->after('price');
            $table->date('published_at')->nullable()->after('format');
            $table->boolean('is_active')->default(true)->after('stock_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['publisher', 'format', 'published_at', 'is_active']);
        });
    }
};
