<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['role_name' => 'Administrator'],
            ['role_name' => 'Manager'],      // Added Manager role
            ['role_name' => 'User'],          // Added User role
            ['role_name' => 'Project Manager'],
            ['role_name' => 'Team Lead'],
            ['role_name' => 'Developer'],
            ['role_name' => 'Designer'],
            ['role_name' => 'Quality Assurance'],
            ['role_name' => 'Client'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'role_name' => $role['role_name'],
                'role_user_id' => null, // Set to null initially
                'role_log_datetime' => Carbon::now()->subDays(rand(0, 30)),
                'role_inactive' => 0,
            ]);
        }
    }
}