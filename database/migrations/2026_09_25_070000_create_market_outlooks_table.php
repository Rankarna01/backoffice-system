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
        Schema::create('market_outlooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('sentiment')->default('neutral'); // bullish, bearish, neutral, volatile
            $table->string('market_category')->default('multi_asset'); // commodities, forex, crypto, indices, multi_asset
            $table->json('featured_pairs')->nullable(); // ['XAUUSD', 'EURUSD', 'DXY', 'BTCUSDT']
            $table->string('time_horizon')->default('weekly'); // weekly, monthly, quarterly, special_report
            $table->string('cover_image_url')->nullable();
            $table->json('key_takeaways')->nullable(); // list of bullet points
            $table->json('support_resistance_levels')->nullable(); // key price levels table
            $table->boolean('is_premium')->default(true);
            $table->string('status')->default('published'); // draft, published, archived
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['market_category', 'sentiment']);
            $table->index('status');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_outlooks');
    }
};
