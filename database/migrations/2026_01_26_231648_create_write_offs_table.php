<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('write_offs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->unsigned();
            $table->decimal('amount', 12, 2);
            $table->enum('type', ['bad_debt', 'dispute', 'adjustment', 'waiver']);
            $table->string('reason');
            $table->text('description')->nullable();
            $table->date('write_off_date');
            $table->enum('status', ['pending', 'approved', 'rejected', 'reversed'])->default('pending');
            $table->bigInteger('approved_by')->unsigned()->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->json('affected_bills')->nullable(); // Array of bill IDs
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('approved_by')->references('id')->on('users');
        });

        Schema::create('collection_activities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->unsigned();
            $table->bigInteger('collection_agent_id')->unsigned()->nullable();
            $table->enum('activity_type', ['call', 'visit', 'email', 'sms', 'letter', 'promise_to_pay', 'payment_arrangement']);
            $table->text('notes');
            $table->date('activity_date');
            $table->date('follow_up_date')->nullable();
            $table->enum('outcome', ['contacted', 'promise_to_pay', 'payment_made', 'no_answer', 'disconnected', 'dispute'])->nullable();
            $table->decimal('promised_amount', 10, 2)->nullable();
            $table->date('promised_date')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('collection_agent_id')->references('id')->on('users');
        });

        Schema::create('aging_buckets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('from_days');
            $table->integer('to_days');
            $table->string('color')->default('#6B7280');
            $table->integer('collection_priority')->default(1);
            $table->text('action_required')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('write_offs');
        Schema::dropIfExists('collection_activities');
        Schema::dropIfExists('aging_buckets');

    }
};
