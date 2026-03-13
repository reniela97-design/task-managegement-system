<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        $systems = [
            ['system_name' => 'ERP System', 'system_user_id' => 1],
            ['system_name' => 'CRM Platform', 'system_user_id' => 2],
            ['system_name' => 'Inventory Management', 'system_user_id' => 2],
            ['system_name' => 'HR Portal', 'system_user_id' => 3],
            ['system_name' => 'E-Commerce Platform', 'system_user_id' => 4],
            ['system_name' => 'Mobile App Backend', 'system_user_id' => 5],
            ['system_name' => 'Analytics Dashboard', 'system_user_id' => 6],
        ];

        foreach ($systems as $system) {
            DB::table('systems')->insert([
                'system_name' => $system['system_name'],
                'system_user_id' => $system['system_user_id'],
                'system_log_datetime' => Carbon::now()->subDays(rand(0, 30)),
                'system_inactive' => 0,
            ]);
        }
    }
}