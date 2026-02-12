<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customer_meter_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('meter_id')->constrained()->onDelete('cascade');

            // Balance fields
            $table->decimal('balance_bf', 12, 2)->default(0);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->decimal('total_billed', 12, 2)->default(0);

            // Installation details at time of assignment
            $table->date('installation_date');
            $table->decimal('initial_reading', 12, 2)->default(0);
            $table->decimal('final_reading', 12, 2)->nullable();

            // Assignment period
            $table->date('assigned_at');
            $table->date('unassigned_at')->nullable();
            $table->string('unassignment_reason')->nullable();

            // Status
            $table->enum('status', ['active', 'closed'])->default('closed');

            // Additional metadata
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['customer_id', 'meter_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['meter_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_meter_balances');
    }
};
