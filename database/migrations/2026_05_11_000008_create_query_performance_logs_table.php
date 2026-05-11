<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('query_performance_logs', function (Blueprint $table) {
            $table->id();
            $table->string('query_type', 50)->index();
            $table->text('query_sql')->nullable();
            $table->float('duration_ms');
            $table->json('bindings')->nullable();
            $table->string('source', 100)->nullable();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('query_performance_logs');
    }
};
