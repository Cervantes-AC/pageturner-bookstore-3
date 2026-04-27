<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_monitoring', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['success', 'failed', 'running'])->default('running');
            $table->string('disk')->default('local');
            $table->bigInteger('size')->nullable(); // bytes
            $table->string('path')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('api_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->string('key'); // user_id or IP
            $table->string('endpoint');
            $table->string('tier')->default('public');
            $table->integer('hit_count')->default(0);
            $table->timestamp('window_start');
            $table->timestamps();
            $table->index(['key', 'endpoint', 'window_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_rate_limits');
        Schema::dropIfExists('backup_monitoring');
    }
};
