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
        Schema::create('voting_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contest_id')->constrained('voting_contests')->cascadeOnDelete();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->string('affiliation')->nullable(); // e.g., organization, school, etc.
            $table->text('bio')->nullable();
            $table->integer('votes_count')->default(0); // denormalized counter
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voting_candidates');
    }
};
