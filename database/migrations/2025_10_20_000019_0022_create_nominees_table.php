<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ Create nominees table
        Schema::create('nominees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voting_contest_id')
                ->constrained('voting_contests')
                ->cascadeOnDelete();
            $table->string('name');
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->string('affiliation')->nullable();
            $table->integer('position')->default(0);
            $table->integer('votes_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['voting_contest_id', 'is_active']);
            $table->index('position');
        });

        // 2️⃣ Drop old table safely
        if (Schema::hasTable('voting_candidates')) {
            // Remove foreign key on votes if exists
            if (Schema::hasTable('votes') && Schema::hasColumn('votes', 'candidate_id')) {
                Schema::table('votes', function (Blueprint $table) {
                    $table->dropForeign(['candidate_id']);
                });
            }
            Schema::dropIfExists('voting_candidates');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('nominees');
    }
};
