<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vote_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('nominee_id')->constrained()->onDelete('cascade');
            $table->integer('votes_count')->default(1);
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamps();

            // Helpful indexes
            $table->index(['user_id', 'status']);
            $table->index(['nominee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_purchases');
    }
};
