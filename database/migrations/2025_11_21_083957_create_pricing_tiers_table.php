<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('min_consumption', 10, 2)->default(0);
            $table->decimal('max_consumption', 10, 2)->nullable(); // null for unlimited
            $table->decimal('rate_per_unit', 10, 4); // Rate per m³ for this tier
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['meter_category_id', 'min_consumption']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pricing_tiers');
    }
};