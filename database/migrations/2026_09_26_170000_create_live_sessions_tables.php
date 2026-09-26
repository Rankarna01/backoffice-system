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
        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentor_profiles')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->dateTime('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes')->default(90);
            $table->string('platform')->default('zoom'); // zoom, youtube_live, google_meet, custom
            $table->string('join_url');
            $table->string('passcode')->nullable();
            $table->unsignedInteger('max_participants')->default(0); // 0 = unlimited
            $table->unsignedInteger('registered_count')->default(0);
            $table->unsignedInteger('attended_count')->default(0);
            $table->string('status')->default('upcoming'); // upcoming, live, completed, cancelled
            $table->string('recording_url')->nullable();
            $table->unsignedSmallInteger('recording_duration_minutes')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('target_tier')->default('all'); // all, free, pro, vip
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'scheduled_at']);
            $table->index('mentor_id');
            $table->index('is_featured');
        });

        Schema::create('live_session_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_session_id')->constrained('live_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('registered_at')->useCurrent();
            $table->boolean('attended')->default(false);
            $table->timestamp('attended_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['live_session_id', 'user_id'], 'session_user_unique');
            $table->index('attended');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_session_registrations');
        Schema::dropIfExists('live_sessions');
    }
};
