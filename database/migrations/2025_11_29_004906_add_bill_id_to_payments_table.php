<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add bill_id column if it doesn't exist
            if (!Schema::hasColumn('payments', 'bill_id')) {
                $table->unsignedBigInteger('bill_id')->nullable()->after('customer_id');
                
                // Add foreign key constraint
                $table->foreign('bill_id')
                      ->references('id')
                      ->on('bills')
                      ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['bill_id']);
            // Then drop the column
            $table->dropColumn('bill_id');
        });
    }
};