<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meters', function (Blueprint $table) {
            // Add indexes for faster searching
            $table->index('meter_number');
            $table->index('status');
            $table->index(['status', 'customer_id']);
            $table->index('customer_id');
            $table->index('meter_category_id');
        });
    }

    public function down()
    {
        Schema::table('meters', function (Blueprint $table) {
            $table->dropIndex(['meter_number']);
            $table->dropIndex(['status']);
            $table->dropIndex(['status', 'customer_id']);
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['meter_category_id']);
        });
    }
};