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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('module_id')->nullable()->constrained('modules')->nullOnDelete();
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->unsignedInteger('passing_score')->default(70);
            $table->unsignedInteger('max_attempts')->nullable();
            $table->unsignedInteger('time_limit_minutes')->nullable();
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_options')->default(false);
            $table->boolean('show_answers')->default(true);
            $table->boolean('is_required')->default(true);
            $table->string('status')->default('published'); // draft, published, archived
            $table->softDeletes();
            $table->timestamps();

            $table->index('course_id');
            $table->index('module_id');
            $table->index('status');
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->string('type')->default('single'); // single, multiple, true_false
            $table->text('question');
            $table->text('explanation')->nullable();
            $table->unsignedInteger('points')->default(10);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['quiz_id', 'sort_order']);
        });

        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('quiz_questions')->cascadeOnDelete();
            $table->text('label');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['question_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_options');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
