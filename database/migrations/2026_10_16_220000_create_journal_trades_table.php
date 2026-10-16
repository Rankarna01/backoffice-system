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
        Schema::create('journal_trades', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Trade timing & instrument
            $table->dateTime('entry_time');
            $table->dateTime('exit_time')->nullable();
            $table->string('pair', 50)->index();
            
            // Action & Status
            // type: long (buy), short (sell)
            $table->enum('type', ['long', 'short'])->default('long');
            // status: open (masih jalan), win (hit TP), loss (hit SL), breakeven, closed
            $table->string('status', 30)->default('open')->index();
            
            // Financials
            $table->string('currency', 10)->default('USD');
            $table->decimal('risk_amount', 15, 2)->default(0);
            $table->decimal('profit_loss', 15, 2)->default(0);
            
            // Strategy, Psychology & Session
            $table->string('setup', 100)->nullable();
            $table->string('emotion', 100)->nullable();
            $table->string('session', 100)->nullable();
            $table->boolean('is_disciplined')->default(true);
            
            // Evaluation notes
            $table->text('notes')->nullable();
            $table->string('screenshot_url')->nullable();
            $table->json('tags')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'entry_time']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_trades');
    }
};
