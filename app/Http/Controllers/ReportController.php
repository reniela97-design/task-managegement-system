<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\System;
use App\Models\Category;
use App\Models\Type;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display Productivity and Aging Reports.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $users = User::where('user_inactive', false)->get();
        $clients = Client::where('client_inactive', false)->get();
        $projects = Project::where('project_inactive', false)->get();
        $systems = System::where('system_inactive', false)->get();
        $categories = Category::where('category_inactive', false)->get();
        $types = Type::where('type_inactive', false)->get();

        $query = Task::with(['project', 'client', 'status', 'assignee'])->where('task_inactive', false);

        if ($user->hasRole('Administrator')) {
            if ($request->filled('user_id')) {
                $query->where('task_assign_to', $request->user_id);
            }
        } else {
            $query->where('task_assign_to', $user->user_id);
        }

        if ($request->filled('client_id')) $query->where('task_client_id', $request->client_id);
        if ($request->filled('project_id')) $query->where('task_project_id', $request->project_id);
        if ($request->filled('category_id')) $query->where('task_category_id', $request->category_id);
        if ($request->filled('system_id')) $query->where('task_system_id', $request->system_id);
        if ($request->filled('type_id')) $query->where('task_type_id', $request->type_id);

        $allTasks = $query->get();
        $filterMonth = $request->input('filter_month');
        $filterYear = $request->input('filter_year', now()->year);

        $productivityTasks = $allTasks->filter(function ($task) use ($filterMonth, $filterYear) {
            if ($task->task_status_id != 3 || !$task->task_date_start || !$task->task_date_end) return false;
            $endDate = Carbon::parse($task->task_date_end);
            if ($filterYear && $endDate->year != $filterYear) return false;
            if ($filterMonth && $endDate->format('m') != str_pad($filterMonth, 2, '0', STR_PAD_LEFT)) return false;
            return true;
        })->map(function ($task) {
            $dateStartStr = Carbon::parse($task->task_date_start)->format('Y-m-d');
            $dateEndStr   = Carbon::parse($task->task_date_end)->format('Y-m-d');
            
            $start = Carbon::parse($dateStartStr . ' ' . ($task->task_time_start ?? '00:00:00'));
            $end   = Carbon::parse($dateEndStr . ' ' . ($task->task_time_end ?? '00:00:00'));
            
            $diff = $start->diff($end);
            $task->duration_string = ($diff->d > 0 ? $diff->d . 'd ' : '') . $diff->h . 'h ' . $diff->i . 'm';
            return $task;
        });

        $agingTasks = $allTasks->filter(function ($task) use ($filterMonth, $filterYear) {
            if ($task->task_status_id == 3) return false;
            $createdDate = Carbon::parse($task->task_log_datetime);
            if ($filterYear && $createdDate->year != $filterYear) return false;
            if ($filterMonth && $createdDate->format('m') != str_pad($filterMonth, 2, '0', STR_PAD_LEFT)) return false;
            return true;
        })->map(function ($task) {
            $task->aging_days = floor(now()->diffInDays(Carbon::parse($task->task_log_datetime)));
            return $task;
        })->sortByDesc('aging_days');

        return view('reports.index', compact('productivityTasks', 'agingTasks', 'users', 'clients', 'projects', 'systems', 'categories', 'types'));
    }

    /**
     * Display the Calendar View.
     */
    public function calendar(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $query = Task::with(['status', 'system', 'client', 'project', 'priority', 'assignee']);

        if ($user->hasRole('Administrator')) {
            if ($request->has('user_id') && !empty($request->user_id)) {
                $query->where('task_assign_to', $request->user_id);
            }
        } else {
            $query->where('task_assign_to', $user->user_id);
        }

        // Removed the whereNotNull restrictions to get ALL tasks immediately upon creation
        $tasks = $query->get();

        // Use flatMap to potentially return multiple calendar events (start marker & finish marker)
        $events = $tasks->flatMap(function ($task) {
            $isCompleted = $task->task_status_id == 3;
            $isEmergency = $task->task_priority_id == 1;
            $isLowPriority = $task->task_priority_id == 3; 

            if ($isCompleted) {
                $color = '#059669'; 
            } elseif ($isEmergency) {
                $color = '#7f1d1d'; 
            } elseif ($isLowPriority) {
                $color = '#64748b'; 
            } else {
                $color = '#1e3a8a'; 
            }

            $calendarEvents = [];

            // Determine the date to place the task on (Due Date, fallback to Creation Date)
            $eventDate = $task->task_due_date 
                ? Carbon::parse($task->task_due_date)->format('Y-m-d') 
                : Carbon::parse($task->task_log_datetime)->format('Y-m-d');

            // 1. Task Due Marker
            $calendarEvents[] = [
                'id' => 'task_due_' . $task->task_id,
                'title' => ($isCompleted ? '✔ ' : '[DUE] ') . "[" . ($task->system->system_name ?? 'Gen') . "] " . $task->task_title,
                'start' => $eventDate, // Placed on due date instead of creation date
                'url' => route('tasks.show', $task->task_id),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'type' => 'task',
                    'full_title' => $task->task_title,
                    'system' => $task->system->system_name ?? 'General',
                    'client' => $task->client->client_name ?? 'Internal',
                    'project' => $task->project->project_name ?? 'N/A',
                    'status' => $task->status->status_name ?? 'Unknown',
                    'due_date' => $task->task_due_date ? Carbon::parse($task->task_due_date)->format('M d, Y') : 'N/A',
                    'assigned_to' => $task->assignee->user_name ?? 'Unassigned',
                    'is_completed' => $isCompleted,
                    'finished_at' => $task->task_date_end ? Carbon::parse($task->task_date_end)->format('M d, Y') : 'N/A',
                    'is_emergency' => $isEmergency
                ]
            ];

            // 2. Task Finished Marker (Only if the task is finished)
            if ($isCompleted && !empty($task->task_date_end)) {
                $calendarEvents[] = [
                    'id' => 'task_finished_' . $task->task_id,
                    'title' => '🏁 [FINISHED] ' . $task->task_title,
                    'start' => Carbon::parse($task->task_date_end)->format('Y-m-d'), // Placed on finish date
                    'url' => route('tasks.show', $task->task_id),
                    'backgroundColor' => '#059669', // Emerald Green for completion
                    'borderColor' => '#059669',
                    'extendedProps' => [
                        'type' => 'task',
                        'full_title' => $task->task_title,
                        'system' => $task->system->system_name ?? 'General',
                        'client' => $task->client->client_name ?? 'Internal',
                        'project' => $task->project->project_name ?? 'N/A',
                        'status' => $task->status->status_name ?? 'Unknown',
                        'due_date' => $task->task_due_date ? Carbon::parse($task->task_due_date)->format('M d, Y') : 'N/A',
                        'assigned_to' => $task->assignee->user_name ?? 'Unassigned',
                        'is_completed' => true,
                        'finished_at' => Carbon::parse($task->task_date_end)->format('M d, Y'),
                        'is_emergency' => false
                    ]
                ];
            }

            return $calendarEvents;
        });

        // Fetch Personal Notes securely for the logged-in user only
        $notes = DB::table('personal_notes')->where('user_id', $user->user_id)->get();
        foreach ($notes as $note) {
            $events->push([
                'id' => 'note_' . $note->id,
                'title' => '📝 Note: ' . str()->limit($note->note_text, 15),
                'start' => Carbon::parse($note->note_date)->format('Y-m-d'),
                'allDay' => true,
                'backgroundColor' => '#fef08a', 
                'borderColor' => '#facc15',
                'textColor' => '#854d0e',
                'extendedProps' => [
                    'type' => 'note',
                    'note_date' => Carbon::parse($note->note_date)->format('Y-m-d'),
                    'note_text' => $note->note_text
                ]
            ]);
        }
        
        // Re-index array so JS JSON parsing doesn't break
        $events = $events->values();

        // 1. Determine which user's tasks to load in the sidebar
        $targetUserId = $user->user_id;
        if ($user->hasRole('Administrator') && $request->filled('user_id')) {
            $targetUserId = $request->user_id;
        }

        // 2. Fetch Tasks for the Side Bar using the correct Target User
        $myTasks = Task::with(['project', 'priority'])
            ->where('task_assign_to', $targetUserId)
            ->where('task_inactive', false)
            ->orderByRaw("CASE WHEN task_due_date IS NULL THEN 1 ELSE 0 END") // Put tasks with no due date at the bottom
            ->orderBy('task_due_date', 'asc')
            ->get();

        // 3. Accurately map statuses (Pending is anything not 2 or 3)
        $sidebarTasks = [
            'working' => $myTasks->where('task_status_id', 2),
            'pending' => $myTasks->whereNotIn('task_status_id', [2, 3]), 
        ];

        $users = User::where('user_inactive', false)->get();
        return view('reports.calendar', compact('events', 'users', 'sidebarTasks'));
    }

    /**
     * AJAX Method to Save/Delete Personal Notes
     */
    public function saveNote(Request $request)
    {
        $request->validate([
            'note_date' => 'required|date',
            'note_text' => 'nullable|string'
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userId = $user->user_id;

        // If the note text is empty, delete it
        if (empty($request->note_text)) {
            DB::table('personal_notes')
                ->where('user_id', $userId)
                ->where('note_date', $request->note_date)
                ->delete();
            return response()->json(['success' => true, 'action' => 'deleted']);
        }

        // Check if note exists
        $exists = DB::table('personal_notes')
            ->where('user_id', $userId)
            ->where('note_date', $request->note_date)
            ->exists();

        // Update or Insert correctly with timestamps
        if ($exists) {
            DB::table('personal_notes')
                ->where('user_id', $userId)
                ->where('note_date', $request->note_date)
                ->update(['note_text' => $request->note_text, 'updated_at' => now()]);
        } else {
            DB::table('personal_notes')->insert([
                'user_id' => $userId,
                'note_date' => $request->note_date,
                'note_text' => $request->note_text,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json(['success' => true, 'action' => 'saved']);
    }
}