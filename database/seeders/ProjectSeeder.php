<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'project_name' => 'ERP Implementation',
                'project_client_id' => 1,
                'project_branch' => 'Main Branch',
                'project_address' => '123 Business Ave, New York, NY 10001',
                'project_user_id' => 2,
            ],
            [
                'project_name' => 'CRM Upgrade',
                'project_client_id' => 2,
                'project_branch' => 'West Coast',
                'project_address' => '456 Tech Blvd, San Francisco, CA 94105',
                'project_user_id' => 2,
            ],
            [
                'project_name' => 'Inventory System',
                'project_client_id' => 3,
                'project_branch' => 'East Coast',
                'project_address' => '789 Industrial Pkwy, Chicago, IL 60607',
                'project_user_id' => 3,
            ],
            [
                'project_name' => 'HR Portal Development',
                'project_client_id' => 4,
                'project_branch' => 'Central',
                'project_address' => '321 Corporate Dr, Dallas, TX 75201',
                'project_user_id' => 4,
            ],
            [
                'project_name' => 'E-Commerce Platform',
                'project_client_id' => 5,
                'project_branch' => 'Digital Hub',
                'project_address' => '654 Online Way, Seattle, WA 98101',
                'project_user_id' => 5,
            ],
            [
                'project_name' => 'Mobile App Development',
                'project_client_id' => 6,
                'project_branch' => 'Innovation Center',
                'project_address' => '987 Mobile St, Austin, TX 78701',
                'project_user_id' => 6,
            ],
            [
                'project_name' => 'Data Analytics Platform',
                'project_client_id' => 7,
                'project_branch' => 'Research Park',
                'project_address' => '147 Data Dr, Boston, MA 02108',
                'project_user_id' => 7,
            ],
        ];

        foreach ($projects as $project) {
            DB::table('projects')->insert([
                'project_name' => $project['project_name'],
                'project_client_id' => $project['project_client_id'],
                'project_branch' => $project['project_branch'],
                'project_address' => $project['project_address'],
                'project_user_id' => $project['project_user_id'],
                'project_log_datetime' => Carbon::now()->subDays(rand(0, 30)),
                'project_inactive' => 0,
            ]);
        }
    }
}