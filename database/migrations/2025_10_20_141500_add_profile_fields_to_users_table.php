<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Organizer fields
            $table->string('company_name')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('website')->nullable();
            $table->text('about')->nullable();
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

            // Vendor fields
            $table->string('business_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->text('services_offered')->nullable();

            // Statistics
            $table->integer('total_events')->default(0);
            $table->integer('total_voting_contests')->default(0);
            $table->integer('total_attendees')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);

            // Verification & rating
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('rating')->default(0);

            // Voting
            $table->boolean('can_vote')->default(true);
            $table->integer('total_votes_cast')->default(0);
            $table->decimal('total_amount_spent', 10, 2)->default(0);
            $table->timestamp('last_vote_at')->nullable();
            $table->json('voting_preferences')->nullable();

            // Attendee
            $table->string('occupation')->nullable();
            $table->string('institution')->nullable();
            $table->string('membership_number')->nullable();
            $table->enum('attendee_type', ['voter', 'event-goer'])->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'company_logo',
                'website',
                'about',
                'address',
                'city',
                'state',
                'country',
                'postal_code',
                'latitude',
                'longitude',
                'tax_id',
                'business_registration_number',
                'social_links',
                'business_hours',
                'business_name',
                'contact_number',
                'services_offered',
                'total_events',
                'total_voting_contests',
                'total_attendees',
                'total_revenue',
                'is_verified',
                'is_featured',
                'rating',
                'can_vote',
                'total_votes_cast',
                'total_amount_spent',
                'last_vote_at',
                'voting_preferences',
                'occupation',
                'institution',
                'membership_number',
                'attendee_type'
            ]);
        });
    }
};
