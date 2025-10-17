<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ✅ Define permissions
        $permissions = [
            // Event permissions
            'view events',
            'create events',
            'edit events',
            'delete events',
            'publish events',
            'manage event categories',

            // Voting permissions
            'view voting',
            'create voting',
            'edit voting',
            'delete voting',
            'manage voting categories',

            // User management
            'view users',
            'edit users',
            'delete users',
            'manage roles',

            // Organizer specific
            'manage own events',
            'manage own voting',
            'view event analytics',
            'view voting analytics',

            // Vendor specific
            'manage services',
            'view bookings',
            'manage bookings',

            // System
            'access admin panel',
            'manage system',
        ];

        // ✅ Create permissions safely (avoiding duplicates)
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ✅ Create roles safely
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $organizerRole = Role::firstOrCreate(['name' => 'organizer']);
        $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
        $attendeeRole = Role::firstOrCreate(['name' => 'attendee']);

        // ✅ Assign permissions per role
        $adminRole->syncPermissions(Permission::all());

        $organizerRole->syncPermissions([
            'view events',
            'create events',
            'edit events',
            'delete events',
            'publish events',
            'view voting',
            'create voting',
            'edit voting',
            'delete voting',
            'manage own events',
            'manage own voting',
            'view event analytics',
            'view voting analytics',
        ]);

        $vendorRole->syncPermissions([
            'view events',
            'manage services',
            'view bookings',
            'manage bookings',
        ]);

        $attendeeRole->syncPermissions([
            'view events',
            'view voting',
        ]);

        // ✅ Optional: Automatically assign admin role to the first admin user
        $adminUser = User::where('email', 'admin@eventsphere.com')->first();
        if ($adminUser && !$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        // ✅ Refresh Spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
