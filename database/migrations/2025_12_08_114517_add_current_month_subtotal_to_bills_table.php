<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->decimal('current_month_subtotal', 10, 2)->default(0)->after('consumption_charge');
            $table->decimal('arrears', 10, 2)->default(0)->after('current_month_subtotal');
            $table->decimal('meter_rent', 10, 2)->default(0)->after('arrears');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            //
        });
    }
};
