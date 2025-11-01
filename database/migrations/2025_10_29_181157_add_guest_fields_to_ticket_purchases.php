<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ticket_purchases', function (Blueprint $table) {
            // Make user_id nullable for guest purchases
            $table->foreignId('user_id')->nullable()->change();

            // Add guest information fields
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();

            // Add QR code fields
            $table->text('qr_code_data')->nullable();
            $table->string('qr_code_hash')->nullable()->unique();
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();

            // Add indexes for better performance
            $table->index(['qr_code_hash', 'status']);
            $table->index(['customer_email', 'status']);
        });
    }

    public function down()
    {
        Schema::table('ticket_purchases', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->dropColumn(['customer_name', 'customer_email', 'customer_phone', 'qr_code_data', 'qr_code_hash', 'is_used', 'used_at']);
        });
    }
};
