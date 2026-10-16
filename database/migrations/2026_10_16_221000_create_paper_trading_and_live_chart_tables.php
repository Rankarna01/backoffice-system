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
        // 1. Live Chart & TradingView Embed Configs
        Schema::create('live_chart_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('default_symbol', 50)->default('OANDA:XAUUSD');
            $table->string('default_interval', 10)->default('60'); // 1, 5, 15, 30, 60, 240, D, W
            $table->string('theme', 20)->default('dark'); // dark, light
            $table->string('locale', 20)->default('id');
            $table->string('timezone', 50)->default('Asia/Jakarta');
            $table->json('watchlist')->nullable(); // Pairs list (Crypto, Forex, Emas)
            $table->boolean('allow_trading')->default(true);
            $table->decimal('default_balance', 15, 2)->default(10000.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('customer_id');
        });

        // 2. Paper / Simulation Trading Accounts (Saldo Member yang bisa di-set dari backoffice)
        Schema::create('paper_trading_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('account_number', 50)->unique();
            $table->decimal('balance', 15, 2)->default(10000.00); // Saldo dapat di-set dari backoffice
            $table->decimal('equity', 15, 2)->default(10000.00);
            $table->string('currency', 10)->default('USD');
            $table->decimal('initial_deposit', 15, 2)->default(10000.00);
            $table->string('status', 30)->default('active'); // active, suspended, reset
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // 3. Paper Trading Positions & Order History
        Schema::create('paper_trading_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_trading_account_id')->constrained('paper_trading_accounts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('symbol', 30)->index(); // XAU/USD, BTC/USDT, etc.
            $table->enum('side', ['buy', 'sell'])->default('buy');
            $table->decimal('units', 15, 4)->default(1.0000); // Lot / Unit size
            $table->decimal('entry_price', 15, 4);
            $table->decimal('current_price', 15, 4)->nullable();
            $table->decimal('stop_loss', 15, 4)->nullable();
            $table->decimal('take_profit', 15, 4)->nullable();
            $table->decimal('profit_loss', 15, 2)->default(0.00);
            $table->string('status', 20)->default('open')->index(); // open, closed, cancelled
            $table->dateTime('opened_at')->useCurrent();
            $table->dateTime('closed_at')->nullable();
            $table->decimal('close_price', 15, 4)->nullable();
            $table->string('close_reason', 50)->nullable(); // manual, sl, tp, admin_closed
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['symbol', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_trading_positions');
        Schema::dropIfExists('paper_trading_accounts');
        Schema::dropIfExists('live_chart_configs');
    }
};
