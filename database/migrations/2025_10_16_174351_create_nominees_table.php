<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nominees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voting_contest_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->integer('position')->default(0);
            $table->integer('votes_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['voting_contest_id', 'is_active']);
            $table->index('position');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nominees');
    }
};
