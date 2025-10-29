<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// Create migration: php artisan make:migration add_voting_contest_id_to_events_table

public function up()
{
    Schema::table('events', function (Blueprint $table) {
        $table->foreignId('voting_contest_id')
              ->nullable()
              ->after('organizer_id')
              ->constrained('voting_contests')
              ->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            //
        });
    }
};
