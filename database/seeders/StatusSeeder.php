<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        // Check if data exists to avoid duplicates
        if (DB::table('status')->count() == 0) {
            DB::table('status')->insert([
                [
                    'status_id' => 1,
                    'status_name' => 'Pending',
                    'status_color' => '#f59e0b', // Optional: Orange
                    'status_log_datetime' => now(),
                    'status_inactive' => false,
                ],
                [
                    'status_id' => 2,
                    'status_name' => 'In Progress',
                    'status_color' => '#3b82f6', // Optional: Blue
                    'status_log_datetime' => now(),
                    'status_inactive' => false,
                ],
                [
                    'status_id' => 3,
                    'status_name' => 'Complete',
                    'status_color' => '#10b981', // Optional: Green
                    'status_log_datetime' => now(),
                    'status_inactive' => false,
                ],
                [
                    'status_id' => 4,
                    'status_name' => 'On Hold',
                    'status_color' => '#ef4444', // Optional: Red
                    'status_log_datetime' => now(),
                    'status_inactive' => false,
                ],
            ]);
        }
    }
}