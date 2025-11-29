<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meters', function (Blueprint $table) {
            $table->foreignId('meter_category_id')->nullable()->after('meter_type')->constrained()->nullOnDelete();
            $table->decimal('balance_bf', 10, 2)->default(0); // Balance brought forward
            $table->decimal('current_balance', 10, 2)->default(0)->after('balance_bf');
            $table->json('additional_charges')->nullable()->after('current_balance');
        });
    }

    public function down()
    {
        Schema::table('meters', function (Blueprint $table) {
            $table->dropForeign(['meter_category_id']);
            $table->dropColumn([
                'meter_category_id',
                'installation_fee',
                'connection_fee',
                'deposit_amount',
                'balance_bf',
                'current_balance',
                'additional_charges'
            ]);
        });
    }
};
