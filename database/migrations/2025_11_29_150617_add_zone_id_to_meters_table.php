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
        Schema::table('meters', function (Blueprint $table) {
            $table->foreignId('zone_id')->nullable()->constrained('zones')->after('meter_category_id')->nullOnDelete();
            $table->foreignId('walk_route_id')->nullable()->constrained('walk_routes')->after('zone_id')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meters', function (Blueprint $table) {
            //
        });
    }
};
