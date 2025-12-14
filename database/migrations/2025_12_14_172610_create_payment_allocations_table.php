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
        // 1. Payment allocations table
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('allocated_to_principal', 10, 2);
            $table->decimal('allocated_to_late_fee', 10, 2);
            $table->date('allocation_date');
            $table->timestamps();

            $table->index(['payment_id', 'bill_id']);
        });

        // 2. Payment reversals table
        Schema::create('payment_reversals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('original_payment_id')->constrained('payments')->onDelete('cascade');
            $table->decimal('reversal_amount', 10, 2);
            $table->date('reversal_date');
            $table->unsignedBigInteger('reversed_by')->nullable();
            $table->string('reason', 500);
            $table->string('refund_method')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 3. Add columns to customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->date('last_payment_date')->nullable();
            $table->decimal('last_payment_amount', 10, 2)->nullable();
            $table->decimal('total_payments', 15, 2)->default(0);
            $table->date('last_bill_date')->nullable();
        });

        // 4. Add columns to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('void_reason')->nullable();
            $table->string('refund_method')->nullable();
            $table->string('receipt_number')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
    }
};
