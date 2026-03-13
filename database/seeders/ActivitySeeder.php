<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            [
                'activity_description' => 'User logged in.',
                'activity_user_id' => 1,
                'activity_ip_address' => '192.168.1.100',
                'activity_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
            ],
            [
                'activity_description' => 'Created new task: Implement User Authentication',
                'activity_user_id' => 2,
                'activity_ip_address' => '192.168.1.101',
                'activity_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/617.0',
            ],
            [
                'activity_description' => 'Updated task status: Fix Payment Gateway Bug',
                'activity_user_id' => 4,
                'activity_ip_address' => '192.168.1.102',
                'activity_agent' => 'Mozilla/5.0 (X11; Linux x86_64) Firefox/121.0',
            ],
            [
                'activity_description' => 'Added new client: ABC Corporation',
                'activity_user_id' => 2,
                'activity_ip_address' => '192.168.1.103',
                'activity_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Edge/120.0.0.0',
            ],
            [
                'activity_description' => 'User logged out.',
                'activity_user_id' => 3,
                'activity_ip_address' => '192.168.1.104',
                'activity_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15',
            ],
            [
                'activity_description' => 'Modified project details: ERP Implementation',
                'activity_user_id' => 2,
                'activity_ip_address' => '192.168.1.105',
                'activity_agent' => 'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605.1.15',
            ],
            [
                'activity_description' => 'Exported task report',
                'activity_user_id' => 5,
                'activity_ip_address' => '192.168.1.106',
                'activity_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/119.0.0.0',
            ],
        ];

        foreach ($activities as $activity) {
            DB::table('activity')->insert([
                'activity_description' => $activity['activity_description'],
                'activity_user_id' => $activity['activity_user_id'],
                'activity_ip_address' => $activity['activity_ip_address'],
                'activity_agent' => $activity['activity_agent'],
                'activity_log_datetime' => Carbon::now()->subHours(rand(1, 168)),
            ]);
        }
    }
}