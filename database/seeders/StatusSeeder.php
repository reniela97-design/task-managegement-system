<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['status_name' => 'Pending', 'status_color' => '#FFC107', 'status_user_id' => 1],
            ['status_name' => 'In Progress', 'status_color' => '#17A2B8', 'status_user_id' => 1],
            ['status_name' => 'Review', 'status_color' => '#007BFF', 'status_user_id' => 1],
            ['status_name' => 'Completed', 'status_color' => '#28A745', 'status_user_id' => 1],
            ['status_name' => 'On Hold', 'status_color' => '#6C757D', 'status_user_id' => 1],
            ['status_name' => 'Cancelled', 'status_color' => '#DC3545', 'status_user_id' => 1],
            ['status_name' => 'Testing', 'status_color' => '#6610F2', 'status_user_id' => 1],
        ];

        foreach ($statuses as $status) {
            DB::table('status')->insert([
                'status_name' => $status['status_name'],
                'status_color' => $status['status_color'],
                'status_user_id' => $status['status_user_id'],
                'status_log_datetime' => Carbon::now()->subDays(rand(0, 30)),
                'status_inactive' => 0,
            ]);
        }
    }
}