<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a checksum column to the audits table for tamper-proof storage.
     * The checksum is a SHA-256 hash of the audit record's key fields.
     */
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->string('checksum', 64)->nullable()->after('tags');
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropColumn('checksum');
        });
    }
};
