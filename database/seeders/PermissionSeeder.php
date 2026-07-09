<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load grouped permissions from config
        $groupedPermissions = config('custom.permissions');

        // Flatten all permissions from all groups
        $allPermissions = [];
        foreach ($groupedPermissions as $group => $permissions) {
            foreach ($permissions as $permission) {
                $allPermissions[] = $permission;
            }
        }

        // Convert to the format expected by the seeder
        $permissions = array_map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        }, $allPermissions);

        // Delete permissions not in the current list
        Permission::whereNotIn('name', array_column($permissions, 'name'))->delete();

        foreach ($permissions as $permission) {
            Permission::updateOrCreate([
                'name' => $permission['name'],
                'guard_name' => $permission['guard_name'],
            ]);
        }
    }
}
