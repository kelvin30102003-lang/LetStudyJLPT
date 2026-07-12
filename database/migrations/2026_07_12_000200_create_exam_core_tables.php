<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('level', 20);
            $table->string('section', 40);
            $table->string('type', 60);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('grammar_rule_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vocabulary_id')->nullable()->constrained('vocabulary')->nullOnDelete();
            $table->foreignId('kanji_id')->nullable()->constrained('kanji')->nullOnDelete();
            $table->foreignId('reading_passage_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('listening_audio_id')->nullable()->constrained('listening_audio')->nullOnDelete();
            $table->text('prompt');
            $table->text('explanation')->nullable();
            $table->unsignedTinyInteger('difficulty')->default(5);
            $table->decimal('points', 6, 2)->default(1);
            $table->string('status', 20)->default('draft');
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['level', 'section', 'type', 'status']);
            $table->index(['category_id', 'subcategory_id']);
            $table->index(['difficulty', 'status']);
            $table->index('published_at');
            $table->index('created_by');
        });

        Schema::create('question_choices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('choice_key', 10);
            $table->text('body');
            $table->boolean('is_correct')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->text('explanation')->nullable();
            $table->timestamps();

            $table->index(['question_id', 'sort_order']);
            $table->index(['question_id', 'is_correct']);
        });

        Schema::create('exams', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title', 190);
            $table->string('level', 20);
            $table->string('exam_type', 40);
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_seconds');
            $table->json('section_rules');
            $table->json('blueprint')->nullable();
            $table->string('status', 20)->default('draft');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['level', 'exam_type', 'status']);
            $table->index('published_at');
        });

        Schema::create('exam_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->restrictOnDelete();
            $table->string('section', 40);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->decimal('points', 6, 2)->default(1);
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->unique(['exam_id', 'question_id']);
            $table->index(['exam_id', 'section', 'sort_order']);
            $table->index('question_id');
        });

        Schema::create('attempts', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_id')->nullable()->constrained()->nullOnDelete();
            $table->string('level', 20);
            $table->string('exam_type', 40);
            $table->string('status', 20)->default('started');
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->decimal('score_raw', 8, 2)->nullable();
            $table->decimal('score_scaled', 8, 2)->nullable();
            $table->boolean('passed')->nullable();
            $table->json('section_scores')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['exam_id', 'status']);
            $table->index(['status', 'expires_at']);
            $table->index('submitted_at');
        });

        Schema::create('attempt_answers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->restrictOnDelete();
            $table->foreignId('selected_choice_id')->nullable()->constrained('question_choices')->nullOnDelete();
            $table->json('answer_payload')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('points_awarded', 6, 2)->default(0);
            $table->timestamp('answered_at')->nullable();
            $table->unsignedInteger('time_spent_seconds')->default(0);
            $table->unsignedInteger('revision_count')->default(0);
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);
            $table->index(['question_id', 'is_correct']);
            $table->index(['attempt_id', 'answered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempt_answers');
        Schema::dropIfExists('attempts');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('question_choices');
        Schema::dropIfExists('questions');
    }
};
