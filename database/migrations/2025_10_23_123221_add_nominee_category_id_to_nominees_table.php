<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nominees', function (Blueprint $table) {
            $table->unsignedBigInteger('nominee_category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nominees', function (Blueprint $table) {
            $table->dropColumn('nominee_category_id');
        });
    }
};
