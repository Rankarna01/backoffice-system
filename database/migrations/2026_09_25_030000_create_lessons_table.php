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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('type')->default('video'); // video, article, file
            $table->longText('content')->nullable();
            $table->string('video_provider')->nullable(); // youtube, vimeo, custom, stream
            $table->string('video_ref')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->boolean('is_preview')->default(false);
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status')->default('draft'); // draft, published, archived
            $table->softDeletes();
            $table->timestamps();

            $table->index(['module_id', 'sort_order']);
            $table->index('course_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
