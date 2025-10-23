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
        Schema::table('voting_contests', function (Blueprint $table) {
            $table->decimal('price_per_vote', 10, 2)->default(10.00)->after('max_votes_per_user');
        });
    }
    
    public function down(): void
    {
        Schema::table('voting_contests', function (Blueprint $table) {
            $table->dropColumn('price_per_vote');
        });
    }
    
};
