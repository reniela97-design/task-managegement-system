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
        
        // User role (ID: 2) created by Admin (user_id: 1)
        DB::table('roles')
            ->where('role_id', 2)
            ->update(['role_user_id' => 1, 'role_log_datetime' => Carbon::now()]);
    }
}