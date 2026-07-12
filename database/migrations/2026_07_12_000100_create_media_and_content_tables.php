<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table): void {
            $table->id();
            $table->string('disk', 40);
            $table->string('path', 1024);
            $table->string('original_name')->nullable();
            $table->string('mime_type', 120)->index();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('checksum', 128)->nullable();
            $table->string('visibility', 20)->default('private');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['disk', 'path']);
            $table->index(['uploaded_by', 'created_at']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('avatar_media_id')->nullable()->after('password')->constrained('media')->nullOnDelete();
        });

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 140)->unique();
            $table->string('type', 40)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'sort_order']);
        });

        Schema::create('subcategories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('slug', 140);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['category_id', 'slug']);
            $table->index(['category_id', 'sort_order']);
        });

        Schema::create('grammar_rules', function (Blueprint $table): void {
            $table->id();
            $table->string('level', 20)->index();
            $table->string('title', 190);
            $table->string('slug', 220)->unique();
            $table->text('structure')->nullable();
            $table->text('meaning');
            $table->text('formation')->nullable();
            $table->json('examples')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['level', 'category_id']);
        });

        Schema::create('vocabulary', function (Blueprint $table): void {
            $table->id();
            $table->string('level', 20)->index();
            $table->string('word', 190);
            $table->string('reading', 190)->nullable()->index();
            $table->text('meaning');
            $table->string('part_of_speech', 80)->nullable();
            $table->text('example_sentence')->nullable();
            $table->foreignId('audio_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->timestamps();

            $table->index(['level', 'word']);
        });

        Schema::create('kanji', function (Blueprint $table): void {
            $table->id();
            $table->string('level', 20)->index();
            $table->string('character', 8)->unique();
            $table->string('onyomi', 190)->nullable();
            $table->string('kunyomi', 190)->nullable();
            $table->text('meaning');
            $table->unsignedTinyInteger('stroke_count')->nullable();
            $table->string('radical', 40)->nullable()->index();
            $table->json('examples')->nullable();
            $table->timestamps();

            $table->index(['level', 'stroke_count']);
        });

        Schema::create('reading_passages', function (Blueprint $table): void {
            $table->id();
            $table->string('level', 20)->index();
            $table->string('title', 190);
            $table->longText('body');
            $table->string('source')->nullable();
            $table->unsignedTinyInteger('difficulty')->default(5);
            $table->unsignedInteger('estimated_seconds')->nullable();
            $table->timestamps();

            $table->index(['level', 'difficulty']);
        });

        Schema::create('listening_audio', function (Blueprint $table): void {
            $table->id();
            $table->string('level', 20)->index();
            $table->string('title', 190);
            $table->foreignId('media_id')->constrained('media')->restrictOnDelete();
            $table->unsignedInteger('duration_seconds');
            $table->longText('transcript')->nullable();
            $table->json('speaker_meta')->nullable();
            $table->timestamps();

            $table->index(['level', 'duration_seconds']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listening_audio');
        Schema::dropIfExists('reading_passages');
        Schema::dropIfExists('kanji');
        Schema::dropIfExists('vocabulary');
        Schema::dropIfExists('grammar_rules');
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('categories');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('avatar_media_id');
        });
        Schema::dropIfExists('media');
    }
};
