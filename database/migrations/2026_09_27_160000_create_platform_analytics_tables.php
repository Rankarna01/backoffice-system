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
        Schema::create('analytics_daily_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date')->unique();
            $table->decimal('gross_revenue', 15, 2)->default(0);
            $table->decimal('net_revenue', 15, 2)->default(0);
            $table->unsignedInteger('new_orders_count')->default(0);
            $table->unsignedInteger('paid_orders_count')->default(0);
            $table->unsignedInteger('new_users_count')->default(0);
            $table->unsignedInteger('active_subscribers_count')->default(0);
            $table->unsignedInteger('course_enrollments_count')->default(0);
            $table->unsignedInteger('lesson_completions_count')->default(0);
            $table->unsignedInteger('quiz_attempts_count')->default(0);
            $table->unsignedInteger('live_attendees_count')->default(0);
            $table->json('category_revenue_distribution')->nullable();
            $table->json('payment_methods_distribution')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('snapshot_date');
        });

        Schema::create('platform_activity_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_name', 100);
            $table->string('entity_type', 100)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('payload')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['event_name', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_activity_events');
        Schema::dropIfExists('analytics_daily_snapshots');
    }
};
