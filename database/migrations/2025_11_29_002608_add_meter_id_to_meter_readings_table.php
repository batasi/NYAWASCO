<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            // Add meter_id column if it doesn't exist
            if (!Schema::hasColumn('meter_readings', 'meter_id')) {
                $table->unsignedBigInteger('meter_id')->nullable()->after('customer_id');
                
                // Add foreign key constraint
                $table->foreign('meter_id')
                      ->references('id')
                      ->on('meters')
                      ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['meter_id']);
            // Then drop the column
            $table->dropColumn('meter_id');
        });
    }
};