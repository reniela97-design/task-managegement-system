<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class GanttController extends Controller
{
    public function index(Request $request): View
    {
        $projectQuery = Project::where('project_inactive', false);
        
        if ($request->filled('project_id')) {
            $projectQuery->where('project_id', $request->project_id);
        }
        
        // Eager load relations to match the Registry logic
        $projectsWithTasks = $projectQuery->with(['tasks' => function($q) use ($request) {
            $q->with(['status', 'priority', 'assignee'])
              ->where('task_inactive', false);
            if ($request->filled('filter_year')) {
                $q->whereYear('task_log_datetime', $request->filter_year);
            }
            $q->orderBy('task_log_datetime', 'asc');
        }])->get();

        $ganttTasks = [];

        foreach ($projectsWithTasks as $project) {
            if ($project->tasks->isEmpty()) continue;

            $projectStart = $project->tasks->min('task_date_start') ?? $project->tasks->min('task_log_datetime') ?? now();
            $projectEnd = $project->tasks->max('task_due_date') ?? $project->tasks->max('task_date_end') ?? Carbon::parse($projectStart)->addDays(3);

            $ganttTasks[] = [
                'id' => 'Proj_' . $project->project_id,
                'name' => strtoupper($project->project_name),
                'start' => Carbon::parse($projectStart)->format('Y-m-d'),
                'end' => Carbon::parse($projectEnd)->format('Y-m-d'),
                'progress' => 0,
                'custom_class' => 'project-header-row',
                'is_project' => true, 
                'project_id' => $project->project_id
            ];

            foreach ($project->tasks as $task) {
                $start = $task->task_date_start ?? $task->task_log_datetime ?? now();
                $end = $task->task_due_date ?? $task->task_date_end ?? Carbon::parse($start)->addDays(3);

                // CSS Classes based on priority
                $class = ($task->task_status_id == 3) ? 'task-completed' : 
                         (($task->task_priority_id == 1) ? 'task-emergency' : 'task-normal');

                $ganttTasks[] = [
                    'id' => 'Task_' . $task->task_id,
                    'name' => $task->task_title,
                    'start' => Carbon::parse($start)->format('Y-m-d'),
                    'end' => Carbon::parse($end)->format('Y-m-d'),
                    'progress' => ($task->task_status_id == 3) ? 100 : (($task->task_status_id == 2) ? 50 : 0),
                    'custom_class' => $class,
                    'is_project' => false,
                    'project_id' => $project->project_id,
                    'url' => route('tasks.show', $task->task_id),
                    // Metadata for Registry-style popup
                    'status_name' => $task->status->status_name ?? 'Pending',
                    'priority_name' => $task->priority->priority_name ?? 'Normal',
                    'assignee_name' => $task->assignee->user_name ?? 'Unassigned'
                ];
            }
        }

        $projects = Project::where('project_inactive', false)->get();
        return view('gantt.index', compact('ganttTasks', 'projects'));
    }
}