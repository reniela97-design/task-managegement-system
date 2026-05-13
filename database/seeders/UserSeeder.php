<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'user_name' => 'Admin',
                'user_email' => 'admin@gmail.com',
                'user_password' => Hash::make('admin'),
                'user_role_id' => 1, // Administrator
            ],
            [
                'user_name' => 'John Manager',
                'user_email' => 'manager@gmail.com',
                'user_password' => Hash::make('password'),
                'user_role_id' => 2, // User
            ],
            [
                'user_name' => 'Jane User',
                'user_email' => 'user@gmail.com',
                'user_password' => Hash::make('password'),
                'user_role_id' => 2, // User
            ],
            [
                'user_name' => 'Mike Johnson',
                'user_email' => 'mike.johnson@example.com',
                'user_password' => Hash::make('password'),
                'user_role_id' => 2, // User
            ],
            [
                'user_name' => 'Sarah Williams',
                'user_email' => 'sarah.williams@example.com',
                'user_password' => Hash::make('password'),
                'user_role_id' => 2, // User
            ],
            [
                'user_name' => 'David Brown',
                'user_email' => 'david.brown@example.com',
                'user_password' => Hash::make('password'),
                'user_role_id' => 2, // User
            ],
            [
                'user_name' => 'Lisa Anderson',
                'user_email' => 'lisa.anderson@example.com',
                'user_password' => Hash::make('password'),
                'user_role_id' => 2, // User
            ],
            [
                'user_name' => 'Robert Chen',
                'user_email' => 'robert.chen@example.com',
                'user_password' => Hash::make('password'),
                'user_role_id' => 2, // User
            ],
            [
                'user_name' => 'Maria Garcia',
                'user_email' => 'maria.garcia@example.com',
                'user_password' => Hash::make('password'),
                'user_role_id' => 2, // User
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert([
                'user_name' => $user['user_name'],
                'user_email' => $user['user_email'],
                'user_password' => $user['user_password'],
                'user_role_id' => $user['user_role_id'],
                'user_inactive' => 0,
                'user_log_datetime' => Carbon::now()->subDays(rand(0, 30)),
            ]);
        }
    }
}