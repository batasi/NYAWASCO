<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_backups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('local'); // local or remote
            $table->string('local_path')->nullable();
            $table->string('remote_host')->nullable();
            $table->string('remote_user')->nullable();
            $table->string('remote_path')->nullable();
            $table->text('ssh_key')->nullable();
            $table->boolean('database_backup')->default(true);
            $table->boolean('reports_backup')->default(false);
            $table->boolean('schedule_enabled')->default(false);
            $table->string('schedule_frequency')->nullable(); // daily, weekly, monthly
            $table->time('schedule_time')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('backup_type')->nullable(); // full, database, files, incremental
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->text('last_run_message')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_backups');
    }
};
