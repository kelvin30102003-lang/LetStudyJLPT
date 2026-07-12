<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mistakes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->restrictOnDelete();
            $table->foreignId('attempt_answer_id')->constrained()->cascadeOnDelete();
            $table->string('reason', 40);
            $table->string('status', 20)->default('open');
            $table->text('user_note')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'created_at']);
            $table->index('question_id');
            $table->index('attempt_id');
        });

        Schema::create('flashcards', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('front');
            $table->text('back');
            $table->string('level', 20)->nullable();
            $table->string('card_type', 40);
            $table->json('metadata')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(['user_id', 'status', 'card_type']);
            $table->index(['source_type', 'source_id']);
        });

        Schema::create('flashcard_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('flashcard_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('scheduled_for')->index();
            $table->unsignedInteger('interval_days')->default(0);
            $table->decimal('ease_factor', 4, 2)->default(2.50);
            $table->unsignedInteger('lapses')->default(0);
            $table->unsignedInteger('response_ms')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'scheduled_for']);
            $table->index(['flashcard_id', 'reviewed_at']);
        });

        Schema::create('recommendations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 60);
            $table->unsignedTinyInteger('priority')->default(5);
            $table->string('title', 190);
            $table->text('reason');
            $table->json('payload')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'priority']);
            $table->index('expires_at');
        });

        Schema::create('dashboard_statistics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('scope', 20);
            $table->string('scope_key', 120);
            $table->date('stat_date');
            $table->json('metrics');
            $table->timestamps();

            $table->unique(['scope', 'scope_key', 'stat_date']);
            $table->index(['user_id', 'stat_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_statistics');
        Schema::dropIfExists('recommendations');
        Schema::dropIfExists('flashcard_reviews');
        Schema::dropIfExists('flashcards');
        Schema::dropIfExists('mistakes');
    }
};
