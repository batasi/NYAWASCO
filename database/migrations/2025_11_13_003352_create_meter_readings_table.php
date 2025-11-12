<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meter_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->decimal('current_reading', 10, 2);
            $table->decimal('previous_reading', 10, 2)->default(0);
            $table->decimal('consumption', 10, 2)->default(0); // current_reading - previous_reading
            $table->date('reading_date');
            $table->enum('reading_type', ['initial', 'monthly', 'special', 'correction'])->default('monthly');
            $table->string('reading_period'); // e.g., "January 2024", "February 2024"
            $table->boolean('billed')->default(false);
            $table->foreignId('billed_by')->nullable()->constrained('users');
            $table->timestamp('billed_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('reading_image')->nullable(); // For evidence
            $table->foreignId('read_by')->constrained('users');
            $table->timestamps();

            // Ensure unique reading per customer per period
            $table->unique(['customer_id', 'reading_period']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('meter_readings');
    }
};