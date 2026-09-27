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
        // 1. Settings Table (key-value per group)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->default('general'); // general, payment, email, legal, disclaimer
            $table->string('name', 100);
            $table->longText('payload')->nullable();
            $table->boolean('locked')->default(false);
            $table->timestamps();

            $table->unique(['group', 'name']);
            $table->index('group');
        });

        // 2. Legal Documents Table (versioned Terms, Privacy, Refund, Disclaimer)
        Schema::create('legal_documents', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50); // terms, privacy, disclaimer, refund
            $table->string('version', 20)->default('1.0');
            $table->string('title');
            $table->longText('body');
            $table->dateTime('effective_at')->nullable();
            $table->boolean('is_current')->default(true);
            $table->timestamps();

            $table->unique(['type', 'version']);
            $table->index(['type', 'is_current']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_documents');
        Schema::dropIfExists('settings');
    }
};
