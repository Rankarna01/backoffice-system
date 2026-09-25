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
        Schema::create('market_news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('category')->default('macro_economy'); // forex, commodities, crypto, indices, central_banks, macro_economy
            $table->string('impact_level')->default('medium'); // high, medium, low
            $table->string('sentiment')->default('neutral'); // bullish, bearish, neutral
            $table->string('source')->nullable(); // Bloomberg, Reuters, Forex Factory, Internal Analyst
            $table->string('source_url')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->json('related_symbols')->nullable(); // ['XAUUSD', 'EURUSD', 'DXY', 'BTCUSDT']
            $table->boolean('is_breaking')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('published'); // draft, published, archived
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['category', 'impact_level']);
            $table->index('status');
            $table->index('is_breaking');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_news');
    }
};
