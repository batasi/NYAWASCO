<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->string('reading_status')->default('recorded')->after('reading_type');
            $table->string('exception_type')->nullable()->after('reading_status');
            $table->text('exception_reason')->nullable()->after('exception_type');
            $table->boolean('estimated')->default(false)->after('exception_reason');
            $table->decimal('estimated_consumption', 10, 2)->nullable()->after('estimated');
            $table->text('exception_evidence')->nullable()->after('estimated_consumption');
        });
    }

    public function down()
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->dropColumn([
                'reading_status',
                'exception_type',
                'exception_reason',
                'estimated',
                'estimated_consumption',
                'exception_evidence'
            ]);
        });
    }
};