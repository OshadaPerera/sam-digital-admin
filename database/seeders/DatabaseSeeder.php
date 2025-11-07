<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Users
        $user1 = User::create([
            'name' => 'oshada',
            'email' => 'oshadhakavinduperera1@gmail.com',
            'password' => Hash::make('*GodFirst1513*'),
            'created_at' => now(),
        ]);
        // Create roles
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'User']);
        // Assign Roles to Users
        $user1->assignRole($superAdminRole);

        $this->call([
            PermissionSeeder::class,
            ReviewSeeder::class,
            AlbumSeeder::class,
        ]);
    }
}
