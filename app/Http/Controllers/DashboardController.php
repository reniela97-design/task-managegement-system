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
        $calendarTasks = (clone $query)->where('task_status_id', '!=', 3)
                                       ->whereNotNull('task_due_date')
                                       ->get();

        $events = $calendarTasks->map(function ($task) {
            $isEmergency = $task->task_priority_id == 1;
            $color = $isEmergency ? '#7f1d1d' : '#1e3a8a'; 
            
            return [
                'id' => $task->task_id,
                'title' => $task->task_title,
                'start' => $task->task_due_date, 
                'allDay' => true,
                'url' => route('tasks.show', $task->task_id),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'priority_sort' => $task->task_priority_id
            ];
        });

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