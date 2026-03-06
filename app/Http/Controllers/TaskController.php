<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Status;
use App\Models\Priority;
use App\Models\System;
use App\Models\Category;
use App\Models\Type;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Notifications\TaskAssigned;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display the Tasks Registry (Advanced Table for Admins/Managers).
     */
    public function registry(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isAdminOrManager = $user->hasRole('Administrator') || $user->hasRole('Manager');

        $query = Task::with(['project', 'client', 'status', 'assignee', 'priority', 'system', 'category', 'type']);
        $query->where('task_inactive', false);

        if ($isAdminOrManager) {
            if ($request->filled('client_id')) $query->where('task_client_id', $request->client_id);
            if ($request->filled('project_id')) $query->where('task_project_id', $request->project_id);
            if ($request->filled('status_id')) $query->where('task_status_id', $request->status_id);
            if ($request->filled('assignee_id')) $query->where('task_assign_to', $request->assignee_id);
        } else {
            $query->where('task_assign_to', $user->user_id);
        }

        $tasks = $query->latest('task_log_datetime')->paginate(20)->withQueryString();

        $clients = Client::where('client_inactive', false)->get();
        $projects = Project::where('project_inactive', false)->get();
        $statuses = Status::where('status_inactive', false)->get();
        $users = User::where('user_inactive', false)->get();

        return view('tasks.registry', compact('tasks', 'clients', 'projects', 'statuses', 'users', 'isAdminOrManager'));
    }

    /**
     * Display a listing of the resource (Kanban View with Analytics).
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isAdminOrManager = $user->hasRole('Administrator') || $user->hasRole('Manager');
        
        $filterMonth = $request->input('filter_month', ''); 
        $filterYear = $request->input('filter_year', '');   

        $unassignedQuery = Task::whereNull('task_assign_to')
                               ->where('task_inactive', false)
                               ->where('task_status_id', '!=', 3) 
                               ->latest('task_log_datetime');
        
        $this->applyDateFilter($unassignedQuery, $filterMonth, $filterYear);
        $unassignedTasks = $unassignedQuery->get();

        $query = Task::where('task_inactive', false)
                     ->with(['project', 'client', 'status', 'assignee', 'priority', 'system', 'category', 'type']);

        $viewingUser = null;

        if ($isAdminOrManager) {
            if ($request->has('user_id') && !empty($request->user_id)) {
                $query->where('task_assign_to', $request->user_id);
                $viewingUser = User::find($request->user_id);
            }
        } else {
            $query->where('task_assign_to', $user->user_id);
        }

        $this->applyDateFilter($query, $filterMonth, $filterYear);
        $allAssigned = $query->orderByRaw("CASE WHEN task_status_id = 3 THEN task_log_datetime ELSE task_due_date END ASC")->get();

        $inProgressTasks = $allAssigned->where('task_status_id', 2);
        $completedTasks  = $allAssigned->where('task_status_id', 3)->sortByDesc('task_date_end'); 
        $pendingTasks    = $allAssigned->where('task_status_id', '!=', 2)->where('task_status_id', '!=', 3);

        $totalTasks = $allAssigned->count();
        $completedCount = $completedTasks->count();
        $activeCount = $inProgressTasks->count() + $pendingTasks->count();
        
        $overdueCount = $allAssigned->where('task_status_id', '!=', 3)
                                    ->filter(function($task) {
                                        return $task->task_due_date && Carbon::parse($task->task_due_date)->endOfDay()->isPast();
                                    })->count();
                                    
        $completionRate = $totalTasks > 0 ? round(($completedCount / $totalTasks) * 100) : 0;

        $groupedCreated = $allAssigned->groupBy(function($item) {
            return Carbon::parse($item->task_log_datetime ?? now())->format('M d');
        });
        $groupedCompleted = $completedTasks->groupBy(function($item) {
            return Carbon::parse($item->task_date_end ?? now())->format('M d');
        });

        $allDates = $groupedCreated->keys()->merge($groupedCompleted->keys())->unique()->sortBy(function($date) {
            return Carbon::parse($date)->timestamp;
        })->values();

        if ($allDates->isEmpty()) $allDates->push(now()->format('M d'));

        $trendLabels = [];
        $trendCreated = [];
        $trendCompleted = [];

        foreach ($allDates as $date) {
            $trendLabels[] = $date;
            $trendCreated[] = $groupedCreated->has($date) ? $groupedCreated[$date]->count() : 0;
            $trendCompleted[] = $groupedCompleted->has($date) ? $groupedCompleted[$date]->count() : 0;
        }

        $chartLabels = json_encode($trendLabels);
        $chartCreated = json_encode($trendCreated);
        $chartCompleted = json_encode($trendCompleted);
        $pieData = json_encode([$pendingTasks->count(), $inProgressTasks->count(), $completedCount]);
        $barData = json_encode([$completedCount, $activeCount, $overdueCount]);

        $users = User::where('user_inactive', false)->get();
        $years = range(2023, now()->addYear()->year);

        return view('tasks.index', compact(
            'inProgressTasks', 'completedTasks', 'pendingTasks', 'unassignedTasks', 
            'users', 'viewingUser', 'filterMonth', 'filterYear', 'years',
            'totalTasks', 'completedCount', 'activeCount', 'overdueCount', 'completionRate',
            'chartLabels', 'chartCreated', 'chartCompleted', 'pieData', 'barData', 'isAdminOrManager'
        ));
    }

    private function applyDateFilter($query, $month, $year)
    {
        if (!empty($year)) $query->whereYear('task_log_datetime', $year);
        if (!empty($month)) $query->whereMonth('task_log_datetime', $month);
    }

    public function create(): View
    {
        $users = User::where('user_inactive', false)->get();
        $clients = Client::where('client_inactive', false)->get();
        $projects = Project::where('project_inactive', false)->get();
        $statuses = Status::where('status_inactive', false)->get();
        $priorities = Priority::where('priority_inactive', false)->get();
        $systems = System::where('system_inactive', false)->get();
        $categories = Category::where('category_inactive', false)->get();
        $types = Type::where('type_inactive', false)->get();

        return view('tasks.create', compact('users', 'clients', 'projects', 'statuses', 'priorities', 'systems', 'categories', 'types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'task_title' => 'required|string|max:255',
            'task_description' => 'nullable|string',
            'task_due_date' => 'nullable|date',
            'task_client_id' => 'nullable|exists:clients,client_id',
            'task_project_id' => 'nullable|exists:projects,project_id',
            'task_status_id' => 'required|exists:status,status_id', 
            'task_priority_id' => 'required|exists:priorities,priority_id',
            'task_assign_to' => 'nullable|exists:users,user_id',
            'task_system_id' => 'nullable|exists:systems,system_id',
            'task_category_id' => 'nullable|exists:categories,category_id',
            'task_type_id' => 'nullable|exists:types,type_id',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $assignTo = $user->user_id;
        
        if (($user->hasRole('Administrator') || $user->hasRole('Manager')) && $request->filled('task_assign_to')) {
            $assignTo = $request->task_assign_to;
        }

        $task = Task::create([
            'task_title' => $validated['task_title'],
            'task_description' => $validated['task_description'],
            'task_assign_to' => $assignTo,
            'task_user_id' => $user->user_id,
            'task_due_date' => $validated['task_due_date'],
            'task_client_id' => $request->task_client_id,
            'task_project_id' => $request->task_project_id,
            'task_status_id' => $request->task_status_id,
            'task_priority_id' => $request->task_priority_id,
            'task_system_id' => $request->task_system_id,
            'task_category_id' => $request->task_category_id,
            'task_type_id' => $request->task_type_id,
            'task_inactive' => false,
        ]);

        $this->logActivity('Created new task: ' . $validated['task_title']);

        if ($task->task_assign_to && $task->task_assign_to !== $user->user_id) {
            /** @var \App\Models\User $assignee */
            $assignee = User::find($task->task_assign_to);
            if ($assignee) {
                $assignee->notify(new TaskAssigned($task));
            }
        }

        return redirect()->route('tasks.index')->with('status', 'Task created successfully!');
    }

    public function show(Task $task): View
    {
        if ($task->task_inactive) abort(404);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->hasRole('Administrator') && !$user->hasRole('Manager') && $task->task_assign_to !== $user->user_id) {
            abort(403, 'Unauthorized access.');
        }
        
        $task->load(['project', 'client', 'status', 'assignee', 'priority', 'system', 'category', 'type']);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        if ($task->task_inactive) abort(404);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->hasRole('Administrator') && !$user->hasRole('Manager') && $task->task_assign_to !== $user->user_id) {
            abort(403, 'You are only allowed to edit tasks assigned to you.');
        }

        $users = User::where('user_inactive', false)->get();
        $clients = Client::where('client_inactive', false)->get();
        $projects = Project::where('project_inactive', false)->get();
        $statuses = Status::where('status_inactive', false)->get();
        $priorities = Priority::where('priority_inactive', false)->get();
        $systems = System::where('system_inactive', false)->get();
        $categories = Category::where('category_inactive', false)->get();
        $types = Type::where('type_inactive', false)->get();

        return view('tasks.edit', compact('task', 'users', 'clients', 'projects', 'statuses', 'priorities', 'systems', 'categories', 'types'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->hasRole('Administrator') && !$user->hasRole('Manager') && $task->task_assign_to !== $user->user_id) {
            abort(403, 'You are only allowed to edit tasks assigned to you.');
        }

        $isAdminOrManager = $user->hasRole('Administrator') || $user->hasRole('Manager');
        $originalAssigneeId = $task->task_assign_to;

        $validated = $request->validate([
            'task_title' => 'required|string|max:255',
            'task_description' => 'nullable|string',
            'task_due_date' => 'nullable|date',
            'task_client_id' => 'nullable|exists:clients,client_id',
            'task_project_id' => 'nullable|exists:projects,project_id',
            'task_status_id' => 'required|exists:status,status_id',
            'task_priority_id' => 'required|exists:priorities,priority_id',
            'task_assign_to' => 'nullable|exists:users,user_id',
            'task_remarks' => 'nullable|string',
            'task_system_id' => 'nullable|exists:systems,system_id',
            'task_category_id' => 'nullable|exists:categories,category_id',
            'task_type_id' => 'nullable|exists:types,type_id',
            'task_date_start' => 'nullable|date',
            'task_time_start' => 'nullable',
            'task_date_end' => 'nullable|date',
            'task_time_end' => 'nullable',
        ]);

        if ($isAdminOrManager) {
            $task->update($validated);
            $task->update(['task_edit_pending' => false, 'task_pending_data' => null]);
            $this->logActivity('Updated task details: ' . $task->task_title);

            if ($task->task_assign_to && $task->task_assign_to != $originalAssigneeId && $task->task_assign_to !== $user->user_id) {
                /** @var \App\Models\User $newAssignee */
                $newAssignee = User::find($task->task_assign_to);
                if ($newAssignee) {
                    $newAssignee->notify(new TaskAssigned($task));
                }
            }

            return redirect()->route('tasks.index')->with('status', 'Task updated successfully!');
        } else {
            $task->update([
                'task_edit_pending' => true,
                'task_pending_data' => json_encode($validated)
            ]);
            $this->logActivity('Submitted task edits for approval: ' . $task->task_title);

            return redirect()->route('tasks.index')->with('status', 'Your changes have been submitted and are awaiting Admin approval.');
        }
    }

    public function approveEdit(Task $task): RedirectResponse
    {
        $this->authorizeAdminOrManager();

        if ($task->task_edit_pending && $task->task_pending_data) {
            $newData = json_decode($task->task_pending_data, true);
            $task->update($newData);
            $task->update(['task_edit_pending' => false, 'task_pending_data' => null]);
            $this->logActivity('Approved task edits for: ' . $task->task_title);
            return back()->with('status', 'Task edits approved and applied successfully!');
        }

        return back()->with('error', 'No pending edits found.');
    }

    public function rejectEdit(Task $task): RedirectResponse
    {
        $this->authorizeAdminOrManager();

        if ($task->task_edit_pending) {
            $task->update(['task_edit_pending' => false, 'task_pending_data' => null]);
            $this->logActivity('Rejected task edits for: ' . $task->task_title);
            return back()->with('status', 'Task edits rejected and discarded.');
        }

        return back()->with('error', 'No pending edits found.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorizeAdminOrManager();
        $task->update(['task_inactive' => true]);
        $this->logActivity('Deleted task: ' . $task->task_title);
        
        return back()->with('status', 'Task deleted successfully!');
    }

    public function claim(Task $task): RedirectResponse
    {
        if ($task->task_assign_to !== null) return back()->with('error', 'This task is already assigned.');

        $task->update(['task_assign_to' => Auth::id()]);
        $this->logActivity('Claimed task: ' . $task->task_title);

        return redirect()->route('tasks.index')->with('status', 'Task claimed successfully!');
    }

    public function start(Task $task): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->user_id !== $task->task_assign_to && !$user->hasRole('Administrator') && !$user->hasRole('Manager')) {
            abort(403, 'You cannot start a task assigned to someone else.');
        }

        $task->update([
            'task_date_start' => now()->toDateString(),
            'task_time_start' => now()->toTimeString(),
            'task_status_id'  => 2, 
        ]);

        $this->logActivity('Started working on task: ' . $task->task_title);

        return back()->with('status', 'Task started! Timer is running.');
    }

    public function finish(Request $request, Task $task): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->user_id !== $task->task_assign_to && !$user->hasRole('Administrator') && !$user->hasRole('Manager')) {
            abort(403, 'You cannot finish a task assigned to someone else.');
        }

        $updates = [
            'task_date_end' => $request->input('task_date_end') ?: now()->toDateString(),
            'task_time_end' => $request->input('task_time_end') ?: now()->toTimeString(),
            'task_status_id' => 3, 
        ];

        if (is_null($task->task_date_start)) {
            $updates['task_date_start'] = $updates['task_date_end'];
            $updates['task_time_start'] = $updates['task_time_end'];
        }

        $task->update($updates);
        $this->logActivity('Completed task: ' . $task->task_title);

        return back()->with('status', 'Task finished! Duration recorded.');
    }

    private function authorizeAdminOrManager()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasRole('Administrator') && !$user->hasRole('Manager')) {
            abort(403, 'Unauthorized action.');
        }
    }
}