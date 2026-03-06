<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Role (role_user_id is NULL initially)
        $adminRole = Role::create([
            'role_name' => 'Administrator',
            'role_inactive' => false,
        ]);

        // 2. Create User
        $adminUser = User::create([
            'user_name' => 'Admin',
            'user_email' => 'admin@gmail.com',
            'user_password' => Hash::make('admin'),
            'user_role_id' => $adminRole->role_id,
            'user_inactive' => false,
        ]);

        // 3. FIX: Now that the user exists, update the Role to point to them
        $adminRole->update([
            'role_user_id' => $adminUser->user_id
        ]);

        // 4. CALL YOUR OTHER SEEDERS HERE:
        $this->call([
            PrioritySeeder::class,
        ]);
    }
}