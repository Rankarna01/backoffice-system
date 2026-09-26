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
        Schema::create('widget_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('widget_type', 50)->default('economic_calendar');
            $table->string('width', 20)->default('100%');
            $table->string('height', 20)->default('650');
            $table->string('color_theme', 20)->default('dark'); // dark, light
            $table->boolean('is_transparent')->default(false);
            $table->string('locale', 20)->default('en'); // en, id_ID, etc.
            $table->string('importance_filter', 20)->default('-1,0,1'); // -1,0,1 / 0,1 / 1
            $table->json('currencies')->nullable(); // array of currencies or null
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['widget_type', 'customer_id'], 'widget_type_customer_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_configs');
    }
};
