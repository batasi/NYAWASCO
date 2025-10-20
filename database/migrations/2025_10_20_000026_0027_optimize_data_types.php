<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // --- USERS TABLE OPTIMIZATION ---
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'name')) {
                    $table->string('name', 191)->change(); // Standardize varchar length
                }
                if (Schema::hasColumn('users', 'email')) {
                    $table->string('email', 191)->change();
                }
                if (Schema::hasColumn('users', 'phone')) {
                    $table->string('phone', 20)->nullable()->change(); // Limit phone number size
                }
            });
        }

        // --- EVENTS TABLE OPTIMIZATION ---
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (Schema::hasColumn('events', 'title')) {
                    $table->string('title', 191)->change();
                }
                if (Schema::hasColumn('events', 'location')) {
                    $table->string('location', 191)->nullable()->change();
                }
            });
        }

        // --- VOTING CONTESTS TABLE OPTIMIZATION ---
        if (Schema::hasTable('voting_contests')) {
            Schema::table('voting_contests', function (Blueprint $table) {
                if (Schema::hasColumn('voting_contests', 'title')) {
                    $table->string('title', 191)->change();
                }
                if (Schema::hasColumn('voting_contests', 'slug')) {
                    $table->string('slug', 191)->change();
                }
            });
        }

        // --- TICKET PURCHASES ENUM STANDARDIZATION ---
        if (Schema::hasTable('ticket_purchases')) {
            Schema::table('ticket_purchases', function (Blueprint $table) {
                // Use raw SQL to modify ENUM safely (Laravel schema builder has limitations)
                DB::statement("
                    ALTER TABLE ticket_purchases
                    MODIFY COLUMN currency ENUM('USD', 'KES', 'EUR', 'GBP')
                    NOT NULL DEFAULT 'KES'
                ");
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('name')->change();
                $table->string('email')->change();
                $table->string('phone')->nullable()->change();
            });
        }

        // Rollback events table
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                $table->string('title')->change();
                $table->string('location')->nullable()->change();
            });
        }

        // Rollback voting_contests table
        if (Schema::hasTable('voting_contests')) {
            Schema::table('voting_contests', function (Blueprint $table) {
                $table->string('title')->change();
                $table->string('slug')->change();
            });
        }

        // Rollback ENUM to varchar (safe default)
        if (Schema::hasTable('ticket_purchases')) {
            DB::statement("
                ALTER TABLE ticket_purchases
                MODIFY COLUMN currency VARCHAR(10)
                NOT NULL DEFAULT 'KES'
            ");
        }
    }
};
