<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('events', function (Blueprint $table) {
        $table->foreignId('category_id')
            ->nullable()
            ->constrained('event_categories')
            ->nullOnDelete()
            ->after('organizer_id');
    });
}

public function down(): void
{
    Schema::table('events', function (Blueprint $table) {
        $table->dropConstrainedForeignId('category_id');
    });
}

};
