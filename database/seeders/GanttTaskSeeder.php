<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use Carbon\Carbon;
use Faker\Factory as Faker;

class GanttTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // 1. Fetch existing foreign keys so we don't cause DB constraint errors!
        // If your tables are empty, it defaults to [1]
        $userIds = DB::table('users')->pluck('user_id')->toArray() ?: [1];
        $projectIds = DB::table('projects')->pluck('project_id')->toArray() ?: [1, 2, 3];
        $clientIds = DB::table('clients')->pluck('client_id')->toArray() ?: [1];
        $statusIds = [1, 2, 3]; // 1: Pending, 2: In Progress, 3: Completed
        $priorityIds = [1, 2, 3]; // 1: High, 2: Medium, 3: Low

        $tasks = [];
        $chunkSize = 200; // Insert 200 at a time for fast performance

        $this->command->info('Generating 1000 tasks for Gantt Chart...');

        for ($i = 0; $i < 1000; $i++) {
            
            // --- GANTT DATE LOGIC ---
            // Create a realistic timeline spread across the last month and next 3 months
            $statusId = $faker->randomElement($statusIds);
            
            // Planned dates (Gantt needs a start and end point)
            $logDate = Carbon::instance($faker->dateTimeBetween('-1 month', '+3 months'));
            $durationDays = $faker->numberBetween(2, 21); // Tasks take 2 to 21 days
            $dueDate = (clone $logDate)->addDays($durationDays);

            // Actual execution dates (Depends on Status)
            $dateStart = null;
            $dateEnd = null;

            if ($statusId == 2) { // In Progress
                $dateStart = $logDate->toDateString();
            } elseif ($statusId == 3) { // Completed
                $dateStart = $logDate->toDateString();
                $actualDuration = $faker->numberBetween(1, $durationDays + 5); // Sometimes they finish early, sometimes late
                $dateEnd = (clone $logDate)->addDays($actualDuration)->toDateString();
            }

            // --- BUILD THE ROW ---
            $tasks[] = [
                'task_title'       => ucfirst($faker->words(random_int(3, 6), true)),
                'task_description' => $faker->realText(100),
                'task_log_datetime'=> $logDate->toDateTimeString(), // Used as "Created/Planned Start"
                'task_due_date'    => $dueDate->toDateString(),     // Used as "Planned Deadline"
                'task_date_start'  => $dateStart,                   // Actual Start
                'task_time_start'  => $dateStart ? '09:00:00' : null,
                'task_date_end'    => $dateEnd,                     // Actual End
                'task_time_end'    => $dateEnd ? '17:00:00' : null,
                
                // Foreign Keys
                'task_client_id'   => $faker->randomElement($clientIds),
                'task_project_id'  => $faker->randomElement($projectIds),
                'task_status_id'   => $statusId,
                'task_priority_id' => $faker->randomElement($priorityIds),
                'task_assign_to'   => $faker->randomElement($userIds),
                'task_user_id'     => $faker->randomElement($userIds), // Creator
                
                // System defaults based on your existing controller
                'task_system_id'   => 1, 
                'task_category_id' => 1,
                'task_type_id'     => 1,
                'task_inactive'    => false,
            ];

            // --- CHUNK INSERT ---
            if (count($tasks) === $chunkSize) {
                Task::insert($tasks);
                $tasks = []; // Reset array
            }
        }

        // Insert any leftover tasks
        if (!empty($tasks)) {
            Task::insert($tasks);
        }

        $this->command->info('Successfully seeded 1000 tasks!');
    }
}