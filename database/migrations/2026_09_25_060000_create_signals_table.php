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
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('market_type'); // forex, commodities, crypto, indices
            $table->string('pair'); // EURUSD, XAUUSD, BTCUSDT, US30, etc.
            $table->string('timeframe')->default('H1'); // M5, M15, H1, H4, D1
            $table->string('action'); // BUY, SELL, BUY_LIMIT, SELL_LIMIT, BUY_STOP, SELL_STOP
            $table->decimal('entry_price', 16, 5);
            $table->decimal('stop_loss', 16, 5);
            $table->decimal('take_profit_1', 16, 5);
            $table->decimal('take_profit_2', 16, 5)->nullable();
            $table->decimal('take_profit_3', 16, 5)->nullable();
            $table->string('risk_reward_ratio')->nullable(); // e.g. "1:3.0"
            $table->string('status')->default('active'); // pending, active, hit_tp1, hit_tp2, hit_tp3, hit_sl, closed, cancelled
            $table->decimal('result_pips', 10, 1)->nullable(); // +140.0, -30.0
            $table->decimal('risk_percentage', 4, 1)->default(1.0); // 1.0%
            $table->text('analysis_notes')->nullable();
            $table->string('chart_image_url')->nullable();
            $table->boolean('is_premium')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['market_type', 'pair']);
            $table->index('status');
            $table->index('action');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};
