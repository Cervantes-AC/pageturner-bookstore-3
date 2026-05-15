<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_reports', function (Blueprint $table) {
            $table->string('model_used')->nullable()->after('provider_used');
        });
    }

    public function down(): void
    {
        Schema::table('ai_reports', function (Blueprint $table) {
            $table->dropColumn('model_used');
        });
    }
};
