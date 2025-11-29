<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            // Add customer_id column
            if (!Schema::hasColumn('bills', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('id');
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            }
            
            // Copy data from user_id to customer_id if needed
            // This would require custom logic based on your data structure
        });
    }

    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};