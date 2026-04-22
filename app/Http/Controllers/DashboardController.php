<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // --- 1. SETUP TASK QUERY ---
        $query = Task::query();

        // Base Filter: Not Deleted (Inactive)
        $query->where('task_inactive', false);

        // --- APPLY DATE FILTERS ---
        $filterMonth = $request->input('filter_month');
        $filterYear = $request->input('filter_year');

        if (!empty($filterYear)) {
            $query->whereYear('task_log_datetime', $filterYear);
        }

        if (!empty($filterMonth)) {
            $query->whereMonth('task_log_datetime', $filterMonth);
        }

        // Permission Scope (Admins/Managers can filter)
        if ($user->hasRole('Administrator') || $user->hasRole('Manager')) {
            if ($request->has('user_id') && !empty($request->user_id)) {
                $query->where('task_assign_to', $request->user_id);
            }
        } else {
            $query->where('task_assign_to', $user->user_id);
        }

        // --- 2. ANALYTICS CALCULATIONS ---

        // A. Total Tasks
        $totalTasks = (clone $query)->count();

        // B. Status Breakdowns for Graph
        // Assuming: 1 = To Do, 2 = In Progress, 3 = Completed
        $notStartedCount = (clone $query)->where('task_status_id', 1)->count();
        $inProgressCount = (clone $query)->where('task_status_id', 2)->count();
        $completedTasks  = (clone $query)->where('task_status_id', 3)->count();

        // C. General Stats
        $totalPending = (clone $query)->where('task_status_id', '!=', 3)->count();
        
        $highPriorityCount = (clone $query)->where('task_status_id', '!=', 3)
                                           ->where('task_priority_id', 1)
                                           ->count();

        // D. Upcoming List
        $upcomingDue = (clone $query)->where('task_status_id', '!=', 3)
                                     ->whereNotNull('task_due_date')
                                     ->orderBy('task_due_date', 'asc')
                                     ->take(5)
                                     ->get();

        // E. Completion Rate
        $completionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // --- 3. CALENDAR EVENTS ---
        $calendarTasks = (clone $query)->get();

        $events = $calendarTasks->flatMap(function ($task) {
            $isCompleted = $task->task_status_id == 3;
            $isEmergency = $task->task_priority_id == 1;

            if ($isCompleted) {
                $color = '#059669'; // Emerald Green
            } elseif ($isEmergency) {
                $color = '#7f1d1d'; // Red
            } else {
                $color = '#1e3a8a'; // Blue
            }

            $calendarEvents = [];

            // 1. Task Marker (Uses Due Date primarily, falls back to Log Date if no Due Date exists)
            $targetDate = $task->task_due_date ?? $task->task_log_datetime;

            $calendarEvents[] = [
                'id' => 'task_event_' . $task->task_id,
                'title' => ($isCompleted ? '✔ ' : '') . $task->task_title, // Removed [NEW] to make it cleaner
                'start' => Carbon::parse($targetDate)->format('Y-m-d'),
                'allDay' => true,
                'url' => route('tasks.show', $task->task_id),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'priority_sort' => $task->task_priority_id
            ];

            // 2. Task Finished Marker (Only if completed)
            if ($isCompleted && !empty($task->task_date_end)) {
                $calendarEvents[] = [
                    'id' => 'task_finished_' . $task->task_id,
                    'title' => '🏁 [FINISHED] ' . $task->task_title,
                    'start' => Carbon::parse($task->task_date_end)->format('Y-m-d'),
                    'allDay' => true,
                    'url' => route('tasks.show', $task->task_id),
                    'backgroundColor' => '#059669',
                    'borderColor' => '#059669',
                    'priority_sort' => $task->task_priority_id
                ];
            }

            return $calendarEvents;
        })->values(); // Ensure it returns a clean, zero-indexed array for JSON parsing

        // --- 4. NOTIFICATIONS ---
        $notifications = Auth::user()
                            ->unreadNotifications()
                            ->latest()
                            ->limit(5)
                            ->get();

        // --- 5. VIEW DATA ---
        $users = User::where('user_inactive', false)->get();

        return view('dashboard', compact(
            'totalTasks',
            'notStartedCount',
            'inProgressCount',
            'completedTasks',
            'totalPending',
            'highPriorityCount',
            'upcomingDue',
            'completionPercentage',
            'events',
            'users',
            'notifications'
        ));
    }
}