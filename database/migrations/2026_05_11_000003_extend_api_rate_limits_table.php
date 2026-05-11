<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend the existing api_rate_limits table with additional columns
        Schema::table('api_rate_limits', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('method', 10)->default('GET');
            $table->integer('attempts')->default(0);
            $table->integer('limit')->default(30);
            $table->boolean('throttled')->default(false);
            $table->text('user_agent')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('api_rate_limits', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'ip_address', 'method', 'attempts', 'limit', 'throttled', 'user_agent']);
        });
    }
};
