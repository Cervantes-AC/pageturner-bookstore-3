<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('query')->comment('The natural language query from the admin');
            $table->text('summary')->nullable()->comment('AI-generated executive summary');
            $table->json('data')->nullable()->comment('Structured data retrieved from DB');
            $table->json('insights')->nullable()->comment('AI-generated insights from the data');
            $table->json('recommendations')->nullable()->comment('AI-generated recommendations');
            $table->text('ai_prompt')->nullable()->comment('Full prompt sent to AI');
            $table->text('ai_raw_response')->nullable()->comment('Raw AI response for audit');
            $table->string('provider_used')->nullable()->comment('Which AI provider generated this');
            $table->integer('tokens_used')->default(0);
            $table->string('status')->default('pending')->comment('pending, generating, completed, failed');
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_reports');
    }
};
