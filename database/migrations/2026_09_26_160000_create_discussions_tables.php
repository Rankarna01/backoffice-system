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
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('general'); // general, technical_analysis, forex, crypto, psychology, qna
            $table->text('content');
            $table->string('status')->default('published'); // published, pinned, hidden, locked, flagged
            $table->string('moderation_status')->default('approved'); // approved, pending_review, flagged, rejected
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('replies_count')->default(0);
            $table->unsignedInteger('reports_count')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'status']);
            $table->index('moderation_status');
            $table->index('is_pinned');
            $table->index('last_activity_at');
        });

        Schema::create('discussion_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained('discussions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('discussion_replies')->nullOnDelete();
            $table->text('content');
            $table->unsignedInteger('likes_count')->default(0);
            $table->boolean('is_solution')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['discussion_id', 'parent_id']);
            $table->index('is_solution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_replies');
        Schema::dropIfExists('discussions');
    }
};
