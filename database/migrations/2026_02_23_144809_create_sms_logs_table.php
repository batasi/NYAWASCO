<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_phone');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('meter_id')->nullable();
            $table->string('sender_id')->nullable();
            $table->text('message');
            $table->string('message_type')->default('manual')->comment('manual, bulk, bill_reminder, payment_receipt, etc.');
            $table->string('status')->default('pending')->comment('pending, sent, failed, delivered');
            $table->string('api_response_code')->nullable();
            $table->text('api_response_message')->nullable();
            $table->text('error_message')->nullable();
            $table->json('api_response')->nullable();
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->integer('retry_count')->default(0);
            $table->decimal('cost', 10, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('meter_id')->references('id')->on('meters')->onDelete('set null');
            $table->foreign('sent_by')->references('id')->on('users')->onDelete('set null');

            $table->index('recipient_phone');
            $table->index('status');
            $table->index('message_type');
            $table->index('sent_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms_logs');
    }
};
