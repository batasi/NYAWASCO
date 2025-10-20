<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * 1️⃣ Create the unified bookings table (if not already exists)
         */
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->morphs('bookable'); // event, ticket, etc.
                $table->string('ticket_number')->nullable()->unique();
                $table->integer('quantity')->default(1);
                $table->decimal('amount', 10, 2)->default(0);
                $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
                $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
                $table->decimal('amount_paid', 10, 2)->default(0);
                $table->string('payment_reference')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'status']);
            });
        }

        /**
         * 2️⃣ Migrate data from old event_bookings (if exists)
         */
        if (Schema::hasTable('event_bookings')) {
            try {
                DB::statement("
                    INSERT INTO bookings (
                        user_id, bookable_type, bookable_id,
                        ticket_number, quantity, amount, status,
                        payment_status, amount_paid, payment_reference,
                        created_at, updated_at
                    )
                    SELECT
                        user_id,
                        'App\\\\Models\\\\Event' AS bookable_type,
                        event_id AS bookable_id,
                        ticket_number,
                        1 AS quantity,
                        amount_paid AS amount,
                        status,
                        payment_status,
                        amount_paid,
                        payment_reference,
                        created_at,
                        updated_at
                    FROM event_bookings
                ");
            } catch (\Throwable $e) {
                info('Event bookings migration skipped or partially failed: ' . $e->getMessage());
            }

            /**
             * 3️⃣ Drop old table after migration
             */
            Schema::dropIfExists('event_bookings');
        }
    }

    public function down(): void
    {
        // Optional rollback to restore event_bookings if needed
        Schema::create('event_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('ticket_number')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->string('payment_reference')->nullable();
            $table->timestamps();
        });

        Schema::dropIfExists('bookings');
    }
};
