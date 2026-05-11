<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrioritySeeder extends Seeder
{
    public function run(): void
    {
        // Ordered specifically to match frontend hardcoded IDs
        $priorities = [
            ['priority_name' => 'High', 'priority_user_id' => 1],   // Becomes ID 1
            ['priority_name' => 'Medium', 'priority_user_id' => 1], // Becomes ID 2 (Your default "Normal" fallback)
            ['priority_name' => 'Low', 'priority_user_id' => 1],    // Becomes ID 3
        ];

        foreach ($priorities as $priority) {
            DB::table('priorities')->insert([
                'priority_name' => $priority['priority_name'],
                'priority_user_id' => $priority['priority_user_id'],
                'priority_log_datetime' => Carbon::now()->subDays(rand(0, 30)),
                'priority_inactive' => 0,
            ]);
        }
    }
}