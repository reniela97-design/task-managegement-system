<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class GanttController extends Controller
{
    public function index(Request $request): View
    {
        // 1. Fetch active tasks
        $query = Task::where('task_inactive', false);

        // Filter by Project
        if ($request->has('project_id') && !empty($request->project_id)) {
            $query->where('task_project_id', $request->project_id);
        }

        // Filter by Year (Matches the dashboard logic)
        $filterYear = $request->input('filter_year');
        if (!empty($filterYear)) {
            $query->whereYear('task_log_datetime', $filterYear);
        }

        $tasks = $query->orderBy('task_log_datetime', 'asc')->get();

        // 2. Format tasks for Frappe Gantt
        $ganttTasks = $tasks->map(function($task) {
            
            // Determine Start Date
            $start = $task->task_date_start ?? $task->task_log_datetime ?? now();
            
            // Determine End Date (Fallback to 3 days after start if no due date)
            $end = $task->task_due_date ?? $task->task_date_end ?? Carbon::parse($start)->addDays(3);

            // Determine Progress based on status
            $progress = 0;
            if ($task->task_status_id == 3) $progress = 100; // Completed
            elseif ($task->task_status_id == 2) $progress = 50; // In Progress

            // Assign Premium CSS classes based on Priority and Status
            $class = 'task-normal'; 
            if ($task->task_status_id == 3) {
                $class = 'task-completed';
            } elseif ($task->task_priority_id == 1) {
                $class = 'task-emergency';
            }

            return [
                'id' => 'Task_' . $task->task_id,
                'name' => $task->task_title,
                'start' => Carbon::parse($start)->format('Y-m-d'),
                'end' => Carbon::parse($end)->format('Y-m-d'),
                'progress' => $progress,
                'dependencies' => '', 
                'custom_class' => $class,
                'url' => route('tasks.show', $task->task_id) 
            ];
        });

        // Pass data to view
        $projects = \App\Models\Project::where('project_inactive', false)->get();

        return view('gantt.index', compact('ganttTasks', 'projects', 'filterYear'));
    }
}