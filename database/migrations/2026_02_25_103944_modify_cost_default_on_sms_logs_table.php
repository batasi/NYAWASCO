<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->decimal('cost', 10, 2)
                  ->default(1.00)
                  ->nullable()
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->decimal('cost', 10, 2)
                  ->nullable()
                  ->default(null)
                  ->change();
        });
    }
};
