<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('voting_contest_id')->constrained()->onDelete('cascade');
            $table->foreignId('nominee_id')->constrained()->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'voting_contest_id']);
            $table->index(['voting_contest_id', 'nominee_id']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('votes');
    }
};
