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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('status_reason')->nullable()->after('status');
            $table->text('status_notes')->nullable()->after('status_reason');
            $table->timestamp('status_updated_at')->nullable()->after('status_notes');
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['status_reason', 'status_notes', 'status_updated_at']);
        });
    }
};
