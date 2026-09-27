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
        // 1. Announcements
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body');
            $table->string('type')->default('info'); // info, warning, promo, maintenance
            $table->string('audience')->default('all'); // all, pro, vip
            $table->json('audience_ids')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->string('status')->default('published'); // draft, published, archived
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_pinned']);
            $table->index('starts_at');
        });

        // 2. Reviews (Course & Platform)
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('title')->nullable();
            $table->text('comment');
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('approved'); // pending, approved, rejected
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'rating']);
            $table->index('course_id');
        });

        // 3. Testimonials (Landing page social proof)
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable(); // e.g. "Full-Time Trader", "Member VIP 2026"
            $table->string('avatar_url', 500)->nullable();
            $table->text('quote');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'sort_order']);
        });

        // 4. FAQs
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('General'); // General, Kursus & Materi, Sinyal & Indikator, Langganan & Billing
            $table->text('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'sort_order']);
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('announcements');
    }
};
