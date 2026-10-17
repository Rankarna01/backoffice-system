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
        Schema::table('mentor_profiles', function (Blueprint $table) {
            $table->string('avatar_url')->nullable()->after('user_id');
            $table->integer('experience_years')->nullable()->after('headline');
            $table->string('trading_style')->nullable()->after('experience_years');
            $table->decimal('win_rate', 5, 2)->nullable()->after('trading_style');
            $table->string('portfolio_url')->nullable()->after('win_rate');
            $table->json('certifications')->nullable()->after('portfolio_url');
            $table->json('portfolio_items')->nullable()->after('certifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentor_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'avatar_url',
                'experience_years',
                'trading_style',
                'win_rate',
                'portfolio_url',
                'certifications',
                'portfolio_items',
            ]);
        });
    }
};
