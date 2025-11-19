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
            $table->unsignedBigInteger('meter_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('payment_no')->unique()->nullable();
            $table->date('payment_date')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('payment_method')->nullable(); // e.g., MPESA, Cash, Bank
            $table->string('transaction_reference')->nullable();
            $table->string('payment_status')->default('pending'); // pending, completed, failed
            $table->text('notes')->nullable();
            $table->timestamps();
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
