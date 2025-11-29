<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meter_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('installation_fee', 10, 2)->default(0.00);
            $table->decimal('connection_fee', 10, 2)->default(0.00);
            $table->decimal('deposit_amount', 10, 2)->default(0.00);
            $table->decimal('base_charge', 10, 2)->default(0);
            $table->decimal('meter_rent', 10, 2)->default(0);
            $table->boolean('has_tiers')->default(false);
            $table->decimal('default_rate', 10, 4)->default(0); // Rate per m³
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('additional_charges')->nullable(); // For installation fees, etc.
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meter_categories');
    }
};
