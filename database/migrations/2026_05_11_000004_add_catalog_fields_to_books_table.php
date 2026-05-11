<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'publisher')) {
                $table->string('publisher', 255)->nullable()->after('author');
            }
            if (!Schema::hasColumn('books', 'format')) {
                $table->string('format', 50)->default('paperback')->after('isbn');
            }
            if (!Schema::hasColumn('books', 'published_at')) {
                $table->date('published_at')->nullable()->after('format');
            }
            if (!Schema::hasColumn('books', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_featured');
            }
            if (!Schema::hasColumn('books', 'cover_image_url')) {
                $table->text('cover_image_url')->nullable()->after('cover_image');
            }
        });

        DB::table('books')->whereNull('published_at')->update([
            'published_at' => DB::raw("DATE(publication_year || '-01-01')")
        ]);
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['publisher', 'format', 'published_at', 'is_active', 'cover_image_url']);
        });
    }
};
