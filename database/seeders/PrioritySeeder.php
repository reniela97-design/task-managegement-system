<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrioritySeeder extends Seeder
{
    public function run(): void
    {
        $priorities = [
            ['priority_name' => 'Critical', 'priority_user_id' => 1],
            ['priority_name' => 'High', 'priority_user_id' => 1],
            ['priority_name' => 'Normal', 'priority_user_id' => 1],
            ['priority_name' => 'Low', 'priority_user_id' => 1],
            ['priority_name' => 'Very Low', 'priority_user_id' => 1],
            ['priority_name' => 'Urgent', 'priority_user_id' => 1],
            ['priority_name' => 'Optional', 'priority_user_id' => 1],
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