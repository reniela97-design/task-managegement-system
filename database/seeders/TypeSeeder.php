<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['type_name' => 'Feature', 'type_user_id' => 1],
            ['type_name' => 'Enhancement', 'type_user_id' => 2],
            ['type_name' => 'Bug', 'type_user_id' => 3],
            ['type_name' => 'Task', 'type_user_id' => 4],
            ['type_name' => 'Sub-task', 'type_user_id' => 5],
            ['type_name' => 'Epic', 'type_user_id' => 6],
            ['type_name' => 'Story', 'type_user_id' => 7],
        ];

        foreach ($types as $type) {
            DB::table('types')->insert([
                'type_name' => $type['type_name'],
                'type_user_id' => $type['type_user_id'],
                'type_log_datetime' => Carbon::now()->subDays(rand(0, 30)),
                'type_inactive' => 0,
            ]);
        }
    }
}