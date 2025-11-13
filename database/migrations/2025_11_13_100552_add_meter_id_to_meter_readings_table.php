<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->foreignId('meter_id')->nullable()->after('customer_id')->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->dropForeign(['meter_id']);
            $table->dropColumn('meter_id');
        });
    }
};