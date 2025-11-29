<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Remove the old unique constraint
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->dropUnique(['customer_id', 'reading_period']);
        });

        // Add new unique constraint that includes meter_id
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->unique(['customer_id', 'meter_id', 'reading_period'], 'meter_readings_customer_meter_period_unique');
        });
    }

    public function down()
    {
        // Remove the new constraint
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->dropUnique(['customer_id', 'meter_id', 'reading_period']);
        });

        // Restore the old constraint
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->unique(['customer_id', 'reading_period']);
        });
    }
};