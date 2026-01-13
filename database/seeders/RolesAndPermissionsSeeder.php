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
            // User management
            'add users',
            'view users',
            'edit users',
            'delete users',
            'manage roles',
            'users.manage',

            // Customer management
            'add customers',
            'view customers',
            'edit customers',
            'delete customers',

            // Permissions management
            'view permissions',
            'edit permissions',

            // Payments management
            'add payments',
            'view payments',
            'edit payments',
            'delete payments',

            // Bills management
            'add bills',
            'view bills',
            'edit bills',
            'delete bills',

            // Meters management
            'add meters',
            'view meters',
            'edit meters',
            'delete meters',

            // Meters readings management
            'add readings',
            'view readings',
            'edit readings',
            'delete readings',

            // applications management
            'add applications',
            'view applications',
            'edit applications',
            'delete applications',

            // reports management
            'add reports',
            'view reports',
            'edit reports',
            'delete reports',

            // System
            'access admin panel',
            'manage system',
        ];

        // ✅ Create permissions safely (avoiding duplicates)
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ✅ Create roles safely
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $ictRole = Role::firstOrCreate(['name' => 'ict', 'guard_name' => 'web']);
        $developerRole = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $ceoRole = Role::firstOrCreate(['name' => 'ceo', 'guard_name' => 'web']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $chiefRole = Role::firstOrCreate(['name' => 'chief', 'guard_name' => 'web']);
        $billerRole = Role::firstOrCreate(['name' => 'biller', 'guard_name' => 'web']);
        $registrarRole = Role::firstOrCreate(['name' => 'registrar', 'guard_name' => 'web']);
        $reportRole = Role::firstOrCreate(['name' => 'report', 'guard_name' => 'web']);

        // ✅ Assign permissions per role
        $developerRole->syncPermissions(Permission::all());
        
        $superAdminRole->syncPermissions(Permission::all());
        
        $adminRole->syncPermissions([
            // User management
            'add users',
            'view users',
            'edit users',
            'delete users',
            'manage roles',
            'users.manage',
            
            // Customer management
            'add customers',
            'view customers',
            'edit customers',
            'delete customers',

            // Permissions management
            'view permissions',
            'edit permissions',

            // Payments management
            'add payments',
            'view payments',
            'edit payments',
            'delete payments',

            // Bills management
            'add bills',
            'view bills',
            'edit bills',
            'delete bills',

            // Meters management
            'add meters',
            'view meters',
            'edit meters',
            'delete meters',

            // Meters readings management
            'add readings',
            'view readings',
            'edit readings',
            'delete readings',

            // applications management
            'add applications',
            'view applications',
            'edit applications',
            'delete applications',

            // reports management
            'add reports',
            'view reports',
            'edit reports',
            'delete reports',
        ]);

        $ictRole->syncPermissions([
            // User management

            'view users',


            // Customer management

            'view customers',


            // Permissions management
            'view permissions',

            // Payments management
            'view payments',

            // Bills management

            'view bills',


            // Meters management

            'view meters',


            // Meters readings management
            'view readings',


            // applications management

            'view applications',


            // reports management

            'view reports',

        ]);

        $managerRole->syncPermissions([
            // User management
            'add users',
            'view users',

            // Customer management
            'add customers',
            'view customers',


            // Payments management
            'add payments',
            'view payments',

            // Bills management
            'add bills',
            'view bills',

            // Meters management
            'add meters',
            'view meters',

            // Meters readings management
            'add readings',
            'view readings',

            // applications management
            'add applications',
            'view applications',

            // reports management
            'add reports',
            'view reports',

        ]);

        $ceoRole->syncPermissions([
            // User management
            'add users',
            'view users',

            // Customer management
            'add customers',
            'view customers',


            // Payments management
            'add payments',
            'view payments',

            // Bills management
            'add bills',
            'view bills',

            // Meters management
            'add meters',
            'view meters',

            // Meters readings management
            'add readings',
            'view readings',

            // applications management
            'add applications',
            'view applications',

            // reports management
            'add reports',
            'view reports',

        ]);

        $chiefRole->syncPermissions([
            // User management
            'add users',
            'view users',

            // Customer management
            'add customers',
            'view customers',


            // Payments management
            'add payments',
            'view payments',

            // Bills management
            'add bills',
            'view bills',

            // Meters management
            'add meters',
            'view meters',

            // Meters readings management
            'add readings',
            'view readings',

            // applications management
            'add applications',
            'view applications',

            // reports management
            'add reports',
            'view reports',

        ]);

        $billerRole->syncPermissions([

            // Bills management
            'add bills',
            'view bills',
            'edit bills',
            'delete bills',

        ]);

        $registrarRole->syncPermissions([

            // Customer management
            'add customers',
            'view customers',

            // Meters management
            'add meters',
            'view meters',

            // Meters readings management
            'add readings',
            'view readings',

            // applications management
            'add applications',
            'view applications',

        ]);

        $reportRole->syncPermissions([

            // reports management
            'add reports',
            'view reports',
            'edit reports',
            'delete reports',

        ]);

        // ✅ Optional: Automatically assign admin role to the first admin user
        $adminUser = User::where('email', 'admin@mail.com')->first();
        if ($adminUser && !$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        $developerUser = User::where('email', 'developer@mail.com')->first();
        if ($developerUser && !$developerUser->hasRole('developer')) {
            $developerUser->assignRole('developer');
        }

        // ✅ Refresh Spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
