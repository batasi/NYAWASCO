<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('balance_bf', 10, 2)->default(0)->after('expected_users');
            $table->decimal('current_balance', 10, 2)->default(0)->after('balance_bf');
            $table->enum('status', ['new', 'active', 'pending_payment', 'sealed', 'terminated'])->default('new')->change();
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['balance_bf', 'current_balance']);
            $table->enum('status', ['active', 'inactive', 'pending', 'suspended'])->default('pending')->change();
        });
    }
};