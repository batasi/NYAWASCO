<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meters', function (Blueprint $table) {
            $table->id();
            $table->string('meter_number')->unique();
            $table->string('meter_type')->default('digital');
            $table->string('status')->default('available'); // available, assigned, faulty
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->date('installation_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meters');
    }
};