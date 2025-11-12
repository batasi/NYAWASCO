<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('id_number')->unique();
            $table->text('physical_address');
            $table->string('postal_address')->nullable();
            $table->string('meter_number')->unique();
            $table->enum('meter_type', ['domestic', 'commercial', 'industrial'])->default('domestic');
            $table->enum('connection_type', ['new', 'existing'])->default('new');
            $table->date('connection_date');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->decimal('initial_meter_reading', 10, 2)->default(0);
            $table->date('initial_reading_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};