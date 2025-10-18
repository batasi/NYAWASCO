<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@eventsphere.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        // Create organizer users
        $organizers = [
            [
                'name' => 'Event Pro Organizers',
                'email' => 'events@eventpro.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'organizer',
            ],
            [
                'name' => 'City Events Ltd',
                'email' => 'info@cityevents.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'organizer',
            ],
        ];

        foreach ($organizers as $organizerData) {
            $organizer = User::create($organizerData);
            $organizer->assignRole('organizer');
        }

        // Create vendor user
        $vendor = User::create([
            'name' => 'Catering Services',
            'email' => 'catering@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'vendor',
        ]);
        $vendor->assignRole('vendor');

        // Create regular users
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'attendee',
            ]);
            $user->assignRole('attendee');
        }
    }
}
