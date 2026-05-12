<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('endpoint');
            $table->integer('hits')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['ip_address', 'endpoint']);
            $table->index(['user_id', 'endpoint']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_rate_limits');
    }
};
