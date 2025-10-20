<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CleanupRedundantTables extends Migration
{
    public function up()
    {
        // First, drop all foreign key constraints that reference the tables we want to remove
        $this->dropForeignKeys();

        // Migrate data from redundant tables to users table
        $this->migrateOrganizerProfiles();
        $this->migrateVendorProfiles();
        $this->migrateAttendeeProfiles();
        $this->migrateGeneralProfiles();

        // Then drop the redundant tables
        Schema::dropIfExists('organizer_profiles');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('attendees');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('organizers');
    }

    private function dropForeignKeys()
    {
        // Drop foreign keys from events table
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['organizer_id']);
        });

        // Drop foreign keys from organizer_profiles table
        Schema::table('organizer_profiles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Drop foreign keys from vendors table
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Drop foreign keys from attendees table
        Schema::table('attendees', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Drop foreign keys from profiles table
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }

    private function migrateOrganizerProfiles()
    {
        if (Schema::hasTable('organizer_profiles')) {
            $organizerProfiles = DB::table('organizer_profiles')->get();

            foreach ($organizerProfiles as $profile) {
                DB::table('users')
                    ->where('id', $profile->user_id)
                    ->where('role', 'organizer')
                    ->update([
                        'company_name' => $profile->company_name,
                        'company_logo' => $profile->company_logo,
                        'website' => $profile->website,
                        'about' => $profile->about,
                        'phone' => $profile->phone,
                        'address' => $profile->address,
                        'city' => $profile->city,
                        'state' => $profile->state,
                        'country' => $profile->country,
                        'postal_code' => $profile->postal_code,
                        'latitude' => $profile->latitude,
                        'longitude' => $profile->longitude,
                        'tax_id' => $profile->tax_id,
                        'business_registration_number' => $profile->business_registration_number,
                        'social_links' => $profile->social_links,
                        'business_hours' => $profile->business_hours,
                        'is_verified' => $profile->is_verified,
                        'is_featured' => $profile->is_featured,
                        'rating' => $profile->rating,
                        'total_events' => $profile->total_events,
                        'total_voting_contests' => $profile->total_voting_contests,
                        'total_attendees' => $profile->total_attendees,
                        'total_revenue' => $profile->total_revenue,
                    ]);
            }
        }
    }

    private function migrateVendorProfiles()
    {
        if (Schema::hasTable('vendors')) {
            $vendorProfiles = DB::table('vendors')->get();

            foreach ($vendorProfiles as $profile) {
                DB::table('users')
                    ->where('id', $profile->user_id)
                    ->where('role', 'vendor')
                    ->update([
                        'business_name' => $profile->business_name,
                        'contact_number' => $profile->contact_number,
                        'address' => $profile->address,
                        'website' => $profile->website,
                        'tax_id' => $profile->tax_id,
                        'services_offered' => $profile->services_offered,
                    ]);
            }
        }
    }

    private function migrateAttendeeProfiles()
    {
        if (Schema::hasTable('attendees')) {
            $attendeeProfiles = DB::table('attendees')->get();

            foreach ($attendeeProfiles as $profile) {
                DB::table('users')
                    ->where('id', $profile->user_id)
                    ->where('role', 'attendee')
                    ->update([
                        'occupation' => $profile->occupation,
                        'institution' => $profile->institution,
                        'membership_number' => $profile->membership_number,
                        'attendee_type' => $profile->attendee_type,
                    ]);
            }
        }
    }

    private function migrateGeneralProfiles()
    {
        if (Schema::hasTable('profiles')) {
            $generalProfiles = DB::table('profiles')->get();

            foreach ($generalProfiles as $profile) {
                DB::table('users')
                    ->where('id', $profile->user_id)
                    ->update([
                        'company_name' => $profile->company_name ?? null,
                        'website' => $profile->website ?? null,
                        'address' => $profile->address ?? null,
                        'city' => $profile->city ?? null,
                        'state' => $profile->state ?? null,
                        'country' => $profile->country ?? null,
                        'postal_code' => $profile->postal_code ?? null,
                        'tax_id' => $profile->tax_id ?? null,
                        'social_links' => $profile->social_links ?? null,
                    ]);
            }
        }
    }

    public function down()
    {
        // This is a destructive migration, so the down method would be complex
        // In production, you should backup before running this migration
    }
}
