<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'deleted_at')) {
                $table->softDeletes();
            }

            if (!Schema::hasColumn('events', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active');
            }
        });

        Schema::table('event_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('event_categories', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('voting_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('voting_categories', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('is_featured');
        });
        Schema::table('event_categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('voting_categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
