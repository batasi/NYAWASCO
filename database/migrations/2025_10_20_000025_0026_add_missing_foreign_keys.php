<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->addForeignKeyIfNotExists('votes', 'user_id', 'users', 'id');
        $this->addForeignKeyIfNotExists('votes', 'voting_contest_id', 'voting_contests', 'id');
        $this->addForeignKeyIfNotExists('votes', 'nominee_id', 'nominees', 'id');

        $this->addForeignKeyIfNotExists('vote_purchases', 'user_id', 'users', 'id');
        $this->addForeignKeyIfNotExists('vote_purchases', 'nominee_id', 'nominees', 'id');

        $this->addForeignKeyIfNotExists('tickets', 'event_id', 'events', 'id');
        $this->addForeignKeyIfNotExists('ticket_purchases', 'user_id', 'users', 'id');
        $this->addForeignKeyIfNotExists('ticket_purchases', 'event_id', 'events', 'id');
        $this->addForeignKeyIfNotExists('ticket_purchases', 'ticket_id', 'tickets', 'id');

        $this->addForeignKeyIfNotExists('events', 'organizer_id', 'users', 'id');
        $this->addForeignKeyIfNotExists('voting_contests', 'organizer_id', 'users', 'id');
        $this->addForeignKeyIfNotExists('voting_contests', 'category_id', 'voting_categories', 'id');
        $this->addForeignKeyIfNotExists('nominees', 'voting_contest_id', 'voting_contests', 'id');
    }

    public function down(): void
    {
        $this->dropForeignKeyIfExists('votes', 'user_id');
        $this->dropForeignKeyIfExists('votes', 'voting_contest_id');
        $this->dropForeignKeyIfExists('votes', 'nominee_id');

        $this->dropForeignKeyIfExists('vote_purchases', 'user_id');
        $this->dropForeignKeyIfExists('vote_purchases', 'nominee_id');

        $this->dropForeignKeyIfExists('tickets', 'event_id');
        $this->dropForeignKeyIfExists('ticket_purchases', 'user_id');
        $this->dropForeignKeyIfExists('ticket_purchases', 'event_id');
        $this->dropForeignKeyIfExists('ticket_purchases', 'ticket_id');

        $this->dropForeignKeyIfExists('events', 'organizer_id');
        $this->dropForeignKeyIfExists('voting_contests', 'organizer_id');
        $this->dropForeignKeyIfExists('voting_contests', 'category_id');
        $this->dropForeignKeyIfExists('nominees', 'voting_contest_id');
    }

    private function addForeignKeyIfNotExists(string $table, string $column, string $referencesTable, string $referencesColumn): void
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) return;

        $fkName = "{$table}_{$column}_foreign";

        $exists = DB::select("SELECT CONSTRAINT_NAME
                              FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                              WHERE TABLE_SCHEMA = DATABASE()
                                AND TABLE_NAME = ?
                                AND COLUMN_NAME = ?
                                AND REFERENCED_TABLE_NAME IS NOT NULL", [$table, $column]);

        if (empty($exists)) {
            Schema::table($table, function (Blueprint $table) use ($column, $referencesTable, $referencesColumn) {
                $table->foreign($column)->references($referencesColumn)->on($referencesTable)->cascadeOnDelete();
            });
        }
    }

    private function dropForeignKeyIfExists(string $table, string $column): void
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) return;

        $fkName = "{$table}_{$column}_foreign";

        $exists = DB::select("SELECT CONSTRAINT_NAME
                              FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                              WHERE TABLE_SCHEMA = DATABASE()
                                AND TABLE_NAME = ?
                                AND CONSTRAINT_NAME = ?", [$table, $fkName]);

        if (!empty($exists)) {
            Schema::table($table, function (Blueprint $table) use ($column) {
                $table->dropForeign([$column]);
            });
        }
    }
};
