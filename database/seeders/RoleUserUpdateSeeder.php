<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleUserUpdateSeeder extends Seeder
{
    public function run(): void
    {
        // Update roles to reference the users who created them
        
        // Administrator role (ID: 1) created by Admin (user_id: 1)
        DB::table('roles')
            ->where('role_id', 1)
            ->update(['role_user_id' => 1, 'role_log_datetime' => Carbon::now()]);
        
        // Manager role (ID: 2) created by Admin (user_id: 1)
        DB::table('roles')
            ->where('role_id', 2)
            ->update(['role_user_id' => 1, 'role_log_datetime' => Carbon::now()]);
        
        // User role (ID: 3) created by Admin (user_id: 1)
        DB::table('roles')
            ->where('role_id', 3)
            ->update(['role_user_id' => 1, 'role_log_datetime' => Carbon::now()]);
        
        // Project Manager role (ID: 4) created by John Doe (user_id: 2)
        DB::table('roles')
            ->where('role_id', 4)
            ->update(['role_user_id' => 2, 'role_log_datetime' => Carbon::now()]);
        
        // Team Lead role (ID: 5) created by Jane Smith (user_id: 3)
        DB::table('roles')
            ->where('role_id', 5)
            ->update(['role_user_id' => 3, 'role_log_datetime' => Carbon::now()]);
        
        // Developer role (ID: 6) created by Mike Johnson (user_id: 4)
        DB::table('roles')
            ->where('role_id', 6)
            ->update(['role_user_id' => 4, 'role_log_datetime' => Carbon::now()]);
        
        // Designer role (ID: 7) created by Sarah Williams (user_id: 5)
        DB::table('roles')
            ->where('role_id', 7)
            ->update(['role_user_id' => 5, 'role_log_datetime' => Carbon::now()]);
        
        // Quality Assurance role (ID: 8) created by David Brown (user_id: 6)
        DB::table('roles')
            ->where('role_id', 8)
            ->update(['role_user_id' => 6, 'role_log_datetime' => Carbon::now()]);
        
        // Client role (ID: 9) created by Lisa Anderson (user_id: 7)
        DB::table('roles')
            ->where('role_id', 9)
            ->update(['role_user_id' => 7, 'role_log_datetime' => Carbon::now()]);
    }
}