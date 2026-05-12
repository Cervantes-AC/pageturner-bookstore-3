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
            $table->string('status'); // success, failed, running
            $table->string('file_path')->nullable();
            $table->bigInteger('size_bytes')->nullable();
            $table->string('disk')->default('local');
            $table->text('output')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_monitoring');
    }
};
