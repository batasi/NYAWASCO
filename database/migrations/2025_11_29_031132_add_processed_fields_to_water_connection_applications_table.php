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
        Schema::table('water_connection_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('processed_by')->nullable()->after('status');
            $table->timestamp('processed_at')->nullable()->after('processed_by');
            $table->text('decline_reason')->nullable()->change(); // Make sure this matches your existing
            
            // Add foreign key constraint
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_connection_applications', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
            $table->dropColumn(['processed_by', 'processed_at']);
        });
    }
};