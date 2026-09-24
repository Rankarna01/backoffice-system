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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('mentor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->foreignId('thumbnail_media_id')->nullable();
            $table->string('preview_video_ref')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->string('language', 10)->default('id');
            $table->enum('access_type', ['free', 'paid', 'subscription'])->default('paid');
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->string('currency', 10)->default('IDR');
            $table->boolean('is_sequential')->default(false);
            $table->boolean('has_certificate')->default(true);
            $table->enum('status', ['draft', 'in_review', 'published', 'unpublished', 'archived'])->default('draft');
            $table->integer('duration_seconds')->default(0);
            $table->integer('lessons_count')->default(0);
            $table->integer('students_count')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0.00);
            $table->integer('rating_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('category_id');
            $table->index('mentor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
