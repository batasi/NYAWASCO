<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('voting_contest_id')
                ->constrained('voting_contests')
                ->cascadeOnDelete();

            $table->foreignId('nominee_id') // singular
                ->constrained('nominees')
                ->cascadeOnDelete();

            $table->timestamp('voted_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('ip_address')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
