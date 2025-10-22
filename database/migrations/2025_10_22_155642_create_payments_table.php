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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // if logged in
            $table->unsignedBigInteger('voting_contest_id');
            $table->unsignedBigInteger('nominee_id');
            $table->string('order_tracking_id')->unique();
            $table->string('merchant_reference');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('KES');
            $table->string('status')->default('PENDING'); // COMPLETED, FAILED, etc.
            $table->string('payment_method')->nullable(); // e.g. M-PESA
            $table->string('phone_number')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
    
            $table->foreign('voting_contest_id')->references('id')->on('voting_contests')->onDelete('cascade');
            $table->foreign('nominee_id')->references('id')->on('nominees')->onDelete('cascade');
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
