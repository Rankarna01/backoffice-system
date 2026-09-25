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
        Schema::create('economic_calendar_configs', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('trading_economics'); // trading_economics
            $table->string('name')->default('Trading Economics API');
            $table->string('api_key')->default('guest:guest'); // 1 kolom untuk input API key
            $table->string('base_url')->default('https://api.tradingeconomics.com');
            $table->string('status')->default('ready'); // ready, connected, error
            $table->timestamp('last_tested_at')->nullable();
            $table->text('last_error_message')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('economic_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('country'); // United States, Euro Area, etc.
            $table->string('currency', 10); // USD, EUR, GBP, JPY, etc.
            $table->string('event_name'); // Non Farm Payrolls, CPI, etc.
            $table->string('impact_level')->default('medium'); // high, medium, low
            $table->string('actual')->nullable();
            $table->string('forecast')->nullable();
            $table->string('previous')->nullable();
            $table->string('unit', 30)->nullable(); // %, K, B, Points
            $table->dateTime('event_date');
            $table->string('period', 50)->nullable();
            $table->string('source')->default('Trading Economics');
            $table->timestamps();

            $table->index(['currency', 'impact_level']);
            $table->index('event_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('economic_calendar_events');
        Schema::dropIfExists('economic_calendar_configs');
    }
};
