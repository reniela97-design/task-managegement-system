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
        // Base query for active projects
        $projectQuery = Project::where('project_inactive', false);
        
        // Filter by Project
        if ($request->filled('project_id')) {
            $projectQuery->where('project_id', $request->project_id);
        }
        
        // Fetch tasks WITHOUT SQL date filters (We will filter them accurately in PHP)
        $projectsWithTasks = $projectQuery->with(['tasks' => function($q) {
            $q->with(['status', 'priority', 'assignee'])
              ->where('task_inactive', false)
              ->orderBy('task_log_datetime', 'asc');
        }])->get();

        $ganttTasks = [];
        $colorPalettes = ['theme-blue', 'theme-green', 'theme-purple', 'theme-orange', 'theme-teal'];
        $projectIndex = 0;

        // Determine the target Date Range for our filter
        $filterYear = $request->input('filter_year');
        $filterMonth = $request->input('filter_month');
        $targetStart = null;
        $targetEnd = null;

        if ($filterYear || $filterMonth) {
            // Default to current year if a month is selected but no year is provided
            $y = $filterYear ?: date('Y');
            
            if ($filterMonth) {
                // If filtering by Month: Range is Start of Month -> End of Month
                $targetStart = Carbon::createFromDate($y, $filterMonth, 1)->startOfMonth();
                $targetEnd = $targetStart->copy()->endOfMonth();
            } else {
                // If filtering only by Year: Range is Jan 1 -> Dec 31
                $targetStart = Carbon::createFromDate($y, 1, 1)->startOfYear();
                $targetEnd = $targetStart->copy()->endOfYear();
            }
        }

        foreach ($projectsWithTasks as $project) {
            $validTasks = [];
            $completedCount = 0;

            foreach ($project->tasks as $task) {
                // Calculate actual Gantt boundaries for this specific task
                $start = Carbon::parse($task->task_date_start ?? $task->task_log_datetime ?? now());
                $end = Carbon::parse($task->task_due_date ?? $task->task_date_end ?? $start->copy()->addDays(3));

                // --- ACCURATE OVERLAP FILTER ---
                if ($targetStart && $targetEnd) {
                    // Check if the task completely misses the selected time window
                    if ($end->endOfDay() < $targetStart || $start->startOfDay() > $targetEnd) {
                        continue; // Hide this task
                    }
                }

                $validTasks[] = [
                    'task' => $task,
                    'start' => $start,
                    'end' => $end
                ];

                if ($task->task_status_id == 3) {
                    $completedCount++;
                }
            }

            // Skip drawing the project entirely if all its tasks were filtered out
            if (empty($validTasks)) continue;

            // Project Start/End boundaries are based purely on the VISIBLE tasks
            $projectStart = collect($validTasks)->min(fn($t) => $t['start']->format('Y-m-d'));
            $projectEnd = collect($validTasks)->max(fn($t) => $t['end']->format('Y-m-d'));
            
            $themeClass = $colorPalettes[$projectIndex % count($colorPalettes)];

            // Recalculate Project Progress based on visible tasks
            $totalTasks = count($validTasks);
            $projectProgress = $totalTasks > 0 ? round(($completedCount / $totalTasks) * 100) : 0;

            // 1. Add the Project Summary Bar
            $ganttTasks[] = [
                'id' => 'Proj_' . $project->project_id,
                // Removed the {$projDays} variable here
                'name' => strtoupper($project->project_name) . " [{$projectProgress}%]",
                'start' => Carbon::parse($projectStart)->format('Y-m-d'),
                'end' => Carbon::parse($projectEnd)->format('Y-m-d'),
                'progress' => $projectProgress, 
                'custom_class' => 'project-summary-bar ' . $themeClass,
                'is_project' => true, 
                'project_id' => $project->project_id
            ];

            // 2. Add the Validated Tasks
            foreach ($validTasks as $vt) {
                $task = $vt['task'];
                $isCompleted = ($task->task_status_id == 3);

                $ganttTasks[] = [
                    'id' => 'Task_' . $task->task_id,
                    // Removed the {$days} variable here
                    'name' => ($isCompleted ? "🔒 " : "") . $task->task_title,
                    'start' => $vt['start']->format('Y-m-d'),
                    'end' => $vt['end']->format('Y-m-d'),
                    'progress' => $isCompleted ? 100 : (($task->task_status_id == 2) ? 50 : 0),
                    'custom_class' => 'task-bar ' . $themeClass . ($isCompleted ? ' completed-task' : ''),
                    'is_project' => false,
                    'project_id' => $project->project_id,
                    'url' => route('tasks.show', $task->task_id),
                    'status_name' => $task->status->status_name ?? 'Pending',
                    'priority_name' => $task->priority->priority_name ?? 'Normal',
                    'assignee_name' => $task->assignee->user_name ?? 'Unassigned',
                    'is_completed' => $isCompleted 
                ];
            }
            $projectIndex++;
        }

        $projects = Project::where('project_inactive', false)->get();
        return view('gantt.index', compact('ganttTasks', 'projects'));
    }
}