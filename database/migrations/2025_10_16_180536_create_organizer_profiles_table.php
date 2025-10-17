<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('company_logo')->nullable();
            $table->text('about')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('tax_id')->nullable();
            $table->string('business_registration_number')->nullable();
            $table->json('social_links')->nullable();
            $table->json('business_hours')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('rating')->default(0);
            $table->integer('total_events')->default(0);
            $table->integer('total_voting_contests')->default(0);
            $table->integer('total_attendees')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['is_verified', 'is_featured']);
            $table->index(['city', 'country']);
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizer_profiles');
    }
};
