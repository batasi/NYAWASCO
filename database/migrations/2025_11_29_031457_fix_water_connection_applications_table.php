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
            // Check if processed_by doesn't exist, then add it
            if (!Schema::hasColumn('water_connection_applications', 'processed_by')) {
                $table->unsignedBigInteger('processed_by')->nullable()->after('status');
                $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            }
            
            // Check if processed_at doesn't exist, then add it
            if (!Schema::hasColumn('water_connection_applications', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('processed_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't drop columns in down() to be safe
    }
};