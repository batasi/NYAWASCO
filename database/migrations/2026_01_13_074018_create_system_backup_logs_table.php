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
        Schema::create('system_backup_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('system_backup_id');
            $table->string('type')->default('manual'); // manual, scheduled
            $table->string('status')->default('success'); // success, failed
            $table->boolean('database_backup')->default(true);
            $table->boolean('reports_backup')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('execution_time')->nullable(); // in seconds
            $table->bigInteger('file_size')->nullable();
            $table->string('file_path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('system_backup_id')->references('id')->on('system_backups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_backup_logs');
    }
};
