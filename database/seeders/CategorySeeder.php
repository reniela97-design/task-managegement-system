<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['category_name' => 'Development', 'category_user_id' => 1],
            ['category_name' => 'Bug Fix', 'category_user_id' => 2],
            ['category_name' => 'Testing', 'category_user_id' => 3],
            ['category_name' => 'Documentation', 'category_user_id' => 4],
            ['category_name' => 'Meeting', 'category_user_id' => 5],
            ['category_name' => 'Research', 'category_user_id' => 6],
            ['category_name' => 'Maintenance', 'category_user_id' => 7],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'category_name' => $category['category_name'],
                'category_user_id' => $category['category_user_id'],
                'category_log_datetime' => Carbon::now()->subDays(rand(0, 30)),
                'category_inactive' => 0,
            ]);
        }
    }
}