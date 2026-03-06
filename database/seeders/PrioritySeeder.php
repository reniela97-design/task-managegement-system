<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrioritySeeder extends Seeder
{
    public function run(): void
    {
        // Check if data exists to avoid duplicates
        if (DB::table('priorities')->count() == 0) {
            DB::table('priorities')->insert([
                [
                    'priority_id' => 1,
                    'priority_name' => 'High',
                    'priority_log_datetime' => now(),
                    'priority_inactive' => false,
                ],
                [
                    'priority_id' => 2,
                    'priority_name' => 'Normal',
                    'priority_log_datetime' => now(),
                    'priority_inactive' => false,
                ],
                [
                    'priority_id' => 3,
                    'priority_name' => 'Low',
                    'priority_log_datetime' => now(),
                    'priority_inactive' => false,
                ],
            ]);
        }
    }
}