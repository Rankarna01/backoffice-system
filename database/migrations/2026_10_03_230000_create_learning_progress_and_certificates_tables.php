<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for course enrollments, lesson progress, quiz attempts, and certificates.
     */
    public function up(): void
    {
        // 1. Course Enrollments
        if (!Schema::hasTable('course_enrollments')) {
            Schema::create('course_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
                $table->decimal('progress_percentage', 5, 2)->default(0.00);
                $table->unsignedInteger('completed_lessons_count')->default(0);
                $table->dateTime('enrolled_at')->useCurrent();
                $table->dateTime('completed_at')->nullable();
                $table->dateTime('expired_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['user_id', 'course_id']);
                $table->index(['user_id', 'progress_percentage']);
            });
        }

        // 2. Lesson Progress
        if (!Schema::hasTable('lesson_progress')) {
            Schema::create('lesson_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
                $table->unsignedInteger('last_watched_seconds')->default(0);
                $table->boolean('is_completed')->default(false);
                $table->dateTime('completed_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'lesson_id']);
                $table->index(['user_id', 'is_completed']);
            });
        }

        // 3. Quiz Attempts
        if (!Schema::hasTable('quiz_attempts')) {
            Schema::create('quiz_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
                $table->decimal('score', 5, 2)->default(0.00);
                $table->boolean('is_passed')->default(false);
                $table->unsignedInteger('attempt_number')->default(1);
                $table->json('answers_payload')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'quiz_id']);
            });
        }

        // 4. Certificates
        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('certificate_number', 50)->unique();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->string('student_name');
                $table->string('course_title');
                $table->dateTime('issued_at')->useCurrent();
                $table->string('verification_url');
                $table->string('pdf_path')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('certificate_number');
                $table->index(['user_id', 'course_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('course_enrollments');
    }
};
