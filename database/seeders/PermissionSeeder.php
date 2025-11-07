<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions from web routes
        $permissions = [
            // Settings permissions
            'edit profile settings',
            'edit password settings',
            'edit appearance settings',

            // User Management permissions
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Business Profile permissions
            'view business profile',
            'create business profile',
            'update business profile',
            'delete business profile',

            // Review permissions
            'view reviews',
            'delete review',
            'activate review',
            'deactivate review',

            // What We Do permissions
            'view what we do',
            'create what we do',
            'edit what we do',
            'delete what we do',
            'activate what we do',
            'deactivate what we do',
            'add what we do images',
            'delete what we do images',

            // Gallery permissions
            'view gallery',
            'create gallery',
            'edit gallery',
            'delete gallery',
            'activate gallery',
            'deactivate gallery',
            'add gallery images',
            'delete gallery images',

            // Video permissions
            'view videos',
            'create videos',
            'edit videos',
            'delete videos',
            'activate videos',
            'deactivate videos',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $this->command->info('Permissions created successfully!');

        // Assign specific permissions to Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            // Admin gets all review, video, gallery, and what we do permissions
            $adminPermissions = [
                // Review permissions
                'view reviews',
                'delete review',
                'activate review',
                'deactivate review',

                // Video permissions
                'view videos',
                'create videos',
                'edit videos',
                'delete videos',
                'activate videos',
                'deactivate videos',

                // Gallery permissions
                'view gallery',
                'create gallery',
                'edit gallery',
                'delete gallery',
                'activate gallery',
                'deactivate gallery',
                'add gallery images',
                'delete gallery images',

                // What We Do permissions
                'view what we do',
                'create what we do',
                'edit what we do',
                'delete what we do',
                'activate what we do',
                'deactivate what we do',
                'add what we do images',
                'delete what we do images',
            ];

            $adminRole->givePermissionTo($adminPermissions);
            $this->command->info('Permissions assigned to Admin role successfully!');
        }

        // Super Admin gets all permissions
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo(Permission::all());
            $this->command->info('All permissions assigned to Super Admin role successfully!');
        }
    }
}
