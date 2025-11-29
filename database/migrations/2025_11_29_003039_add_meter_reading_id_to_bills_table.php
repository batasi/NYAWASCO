<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            // Add meter_reading_id column if it doesn't exist
            if (!Schema::hasColumn('bills', 'meter_reading_id')) {
                $table->unsignedBigInteger('meter_reading_id')->nullable()->after('meter_id');
                
                // Add foreign key constraint
                $table->foreign('meter_reading_id')
                      ->references('id')
                      ->on('meter_readings')
                      ->onDelete('set null');
            }

            // Also add customer_id if it doesn't exist (replace user_id)
            if (!Schema::hasColumn('bills', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('id');
                
                // Add foreign key constraint
                $table->foreign('customer_id')
                      ->references('id')
                      ->on('customers')
                      ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['meter_reading_id']);
            $table->dropForeign(['customer_id']);
            
            // Then drop the columns
            $table->dropColumn('meter_reading_id');
            $table->dropColumn('customer_id');
        });
    }
};