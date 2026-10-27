<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (! Schema::hasColumn('plans', 'tier')) {
                $table->string('tier', 20)->default('standard')->after('slug');
            }
            if (! Schema::hasColumn('plans', 'badge')) {
                $table->string('badge', 50)->nullable()->after('tier');
            }
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'tier')) {
                $table->dropColumn('tier');
            }
            if (Schema::hasColumn('plans', 'badge')) {
                $table->dropColumn('badge');
            }
        });
    }
};
