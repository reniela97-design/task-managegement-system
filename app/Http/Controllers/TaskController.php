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
     * Map database statuses to system categories based on text match.
     */
    private function getStatusMapping()
    {
        $statuses = Status::all();
        $mapping = [
            'pending' => [],
            'progress' => [],
            'completed' => [],
            'onhold' => [],
            'canceled' => []
        ];

        foreach ($statuses as $status) {
            $name = strtolower($status->status_name);
            if (str_contains($name, 'complete')) {
                $mapping['completed'][] = $status->status_id;
            } elseif (str_contains($name, 'progress')) {
                $mapping['progress'][] = $status->status_id;
            } elseif (str_contains($name, 'cancel')) {
                $mapping['canceled'][] = $status->status_id;
            } elseif (str_contains($name, 'hold')) {
                $mapping['onhold'][] = $status->status_id;
            } else {
                $mapping['pending'][] = $status->status_id;
            }
        }

        // Fallbacks to avoid empty array errors in whereIn
        if (empty($mapping['pending'])) $mapping['pending'] = [-1];
        if (empty($mapping['progress'])) $mapping['progress'] = [-1];
        if (empty($mapping['completed'])) $mapping['completed'] = [-1];
        if (empty($mapping['onhold'])) $mapping['onhold'] = [-1];
        if (empty($mapping['canceled'])) $mapping['canceled'] = [-1];

        return $mapping;
    }

    /**
     * Get only the 5 allowed statuses for dropdowns.
     */
    private function getValidStatuses()
    {
        return Status::where('status_inactive', false)
            ->where(function($query) {
                $query->where('status_name', 'like', '%pending%')
                      ->orWhere('status_name', 'like', '%progress%')
                      ->orWhere('status_name', 'like', '%complete%')
                      ->orWhere('status_name', 'like', '%hold%')
                      ->orWhere('status_name', 'like', '%cancel%');
            })->get();
    }

    /**
     * Display the Tasks Registry (Advanced Table for Admins/Managers).
     */
    public function registry(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Gibilin lang nako ang variable name aron dili maguba ang blade kung gigamit kini didto
        $isAdminOrManager = $user->hasRole('Administrator');

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

        $statusMap = $this->getStatusMapping();

        $inProgressTasks = (clone $query)->whereIn('task_status_id', $statusMap['progress'])
                                         ->latest('task_log_datetime')
                                         ->paginate(5, ['*'], 'progress_page')
                                         ->onEachSide(1)
                                         ->withQueryString();

        $pendingTasks = (clone $query)->whereIn('task_status_id', $statusMap['pending'])
                                      ->orderBy('task_priority_id', 'asc') 
                                      ->latest('task_log_datetime')
                                      ->paginate(5, ['*'], 'todo_page')
                                      ->onEachSide(1)
                                      ->withQueryString();

        $completedTasks = (clone $query)->whereIn('task_status_id', $statusMap['completed'])
                                        ->latest('task_date_end')
                                        ->paginate(5, ['*'], 'completed_page')
                                        ->onEachSide(1)
                                        ->withQueryString();

        $onHoldTasks = (clone $query)->whereIn('task_status_id', $statusMap['onhold'])
                                     ->latest('task_log_datetime')
                                     ->paginate(5, ['*'], 'onhold_page')
                                     ->onEachSide(1)
                                     ->withQueryString();

        $canceledTasks = (clone $query)->whereIn('task_status_id', $statusMap['canceled'])
                                       ->latest('task_log_datetime')
                                       ->paginate(5, ['*'], 'canceled_page')
                                       ->onEachSide(1)
                                       ->withQueryString();

        $hasAnyTasks = $inProgressTasks->total() > 0 || $pendingTasks->total() > 0 || $completedTasks->total() > 0 || $onHoldTasks->total() > 0 || $canceledTasks->total() > 0;

        $clients = Client::where('client_inactive', false)->get();
        $projects = Project::where('project_inactive', false)->get();
        $statuses = $this->getValidStatuses();
        $users = User::where('user_inactive', false)->get();

        return view('tasks.registry', compact('inProgressTasks', 'pendingTasks', 'completedTasks', 'onHoldTasks', 'canceledTasks', 'hasAnyTasks', 'clients', 'projects', 'statuses', 'users', 'isAdminOrManager'));
    }

    /**
     * Display a listing of the resource (Kanban View with Analytics).
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isAdminOrManager = $user->hasRole('Administrator');
        
        $filterMonth = $request->input('filter_month', ''); 
        $filterYear = $request->input('filter_year', '');   
        $filterProject = $request->input('filter_project', ''); 
        
        // 1. Specific Search Inputs
        $searchTodo = $request->input('search_todo', '');
        $searchProgress = $request->input('search_progress', '');
        $searchCompleted = $request->input('search_completed', '');

        // NEW: Fetch all active projects for the dropdown
        $projects = Project::where('project_inactive', false)->get(); 

        $statusMap = $this->getStatusMapping();

        // 2. Base query for Unassigned Pool
        $unassignedQuery = Task::whereNull('task_assign_to')
                               ->where('task_inactive', false)
                               ->whereNotIn('task_status_id', $statusMap['completed']) 
                               ->latest('task_log_datetime');
                               
        // NEW: Apply project filter to unassigned
        if (!empty($filterProject)) {
            $unassignedQuery->where('task_project_id', $filterProject);
        }
        
        $this->applyDateFilter($unassignedQuery, $filterMonth, $filterYear);
        $unassignedTasks = $unassignedQuery->get();

        // 3. Base query for Assigned Tasks
        $baseQuery = Task::where('task_inactive', false)
                         ->with(['project', 'client', 'status', 'assignee', 'priority', 'system', 'category', 'type']);

        $viewingUser = null;

        if ($isAdminOrManager) {
            if ($request->has('user_id') && !empty($request->user_id)) {
                $baseQuery->where('task_assign_to', $request->user_id);
                $viewingUser = User::find($request->user_id);
            }
        } else {
            $baseQuery->where('task_assign_to', $user->user_id);
        }

        $this->applyDateFilter($baseQuery, $filterMonth, $filterYear);

        // NEW: Apply project filter to assigned queries
        if (!empty($filterProject)) {
            $baseQuery->where('task_project_id', $filterProject);
        }

        // 4. Fetch Unpaginated for Analytics/Charts (Stats stay accurate regardless of search)
        $completedIdsCsv = implode(',', $statusMap['completed']);
        $allAssigned = (clone $baseQuery)->orderByRaw("CASE WHEN task_status_id IN ($completedIdsCsv) THEN task_log_datetime ELSE task_due_date END ASC")->get();

        // 5. Fetch Paginated for Kanban Columns with Partial Match Filtering
        $approvalTasks = (clone $baseQuery)->where('task_edit_pending', true)->get();

        $cleanPending = (clone $baseQuery)->whereIn('task_status_id', $statusMap['pending'])
            ->where('task_edit_pending', false)
            ->when($searchTodo, function ($query, $searchTodo) {
                $query->where(function($q) use ($searchTodo) {
                    $q->where('task_title', 'like', "%{$searchTodo}%")
                      ->orWhereHas('project', fn($pq) => $pq->where('project_name', 'like', "%{$searchTodo}%"));
                });
            })
            ->orderBy('task_priority_id', 'asc')
            ->latest('task_log_datetime')
            ->paginate(5, ['*'], 'todo_page')
            ->onEachSide(1) // <--- ADD THIS HERE
            ->withQueryString();

        $cleanInProgress = (clone $baseQuery)->whereIn('task_status_id', $statusMap['progress'])
            ->where('task_edit_pending', false)
            ->when($searchProgress, function ($query, $searchProgress) {
                $query->where(function($q) use ($searchProgress) {
                    $q->where('task_title', 'like', "%{$searchProgress}%")
                      ->orWhereHas('project', fn($pq) => $pq->where('project_name', 'like', "%{$searchProgress}%"));
                });
            })
            ->orderBy('task_priority_id', 'asc')
            ->latest('task_log_datetime')
            ->paginate(5, ['*'], 'progress_page')
            ->onEachSide(1) // <--- ADD THIS HERE
            ->withQueryString();

        $cleanCompleted = (clone $baseQuery)->whereIn('task_status_id', $statusMap['completed'])
            ->where('task_edit_pending', false)
            ->when($searchCompleted, function ($query, $searchCompleted) {
                $query->where(function($q) use ($searchCompleted) {
                    $q->where('task_title', 'like', "%{$searchCompleted}%")
                      ->orWhereHas('project', fn($pq) => $pq->where('project_name', 'like', "%{$searchCompleted}%"));
                });
            })
            ->latest('task_date_end')
            ->paginate(5, ['*'], 'completed_page')
            ->onEachSide(1) // <--- ADD THIS HERE
            ->withQueryString();

        // 6. Analytics Variables Calculation
        $inProgressTasksForStats = $allAssigned->whereIn('task_status_id', $statusMap['progress']);
        $completedTasksForStats  = $allAssigned->whereIn('task_status_id', $statusMap['completed']); 
        $pendingTasksForStats    = $allAssigned->whereIn('task_status_id', $statusMap['pending']);

        $totalTasks = $allAssigned->count();
        $completedCount = $completedTasksForStats->count();
        $activeCount = $inProgressTasksForStats->count() + $pendingTasksForStats->count();
        
        $overdueCount = $allAssigned->whereNotIn('task_status_id', $statusMap['completed'])
                                    ->filter(function($task) {
                                        return $task->task_due_date && Carbon::parse($task->task_due_date)->endOfDay()->isPast();
                                    })->count();
                                    
        $completionRate = $totalTasks > 0 ? round(($completedCount / $totalTasks) * 100) : 0;

        $groupedCreated = $allAssigned->groupBy(function($item) {
            return Carbon::parse($item->task_log_datetime ?? now())->format('M d');
        });
        $groupedCompleted = $completedTasksForStats->groupBy(function($item) {
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
        $pieData = json_encode([$pendingTasksForStats->count(), $inProgressTasksForStats->count(), $completedCount]);
        $barData = json_encode([$completedCount, $activeCount, $overdueCount]);

        $users = User::where('user_inactive', false)->get();
        $years = range(2023, now()->addYear()->year);

        return view('tasks.index', compact(
            'cleanPending', 'cleanInProgress', 'cleanCompleted', 'approvalTasks', 'unassignedTasks', 
            'users', 'viewingUser', 'filterMonth', 'filterYear', 'filterProject', 'projects', 'years', 'searchTodo', 'searchProgress', 'searchCompleted',
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
        $statuses = $this->getValidStatuses();
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
        
        if ($user->hasRole('Administrator') && $request->filled('task_assign_to')) {
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
        
        if (!$user->hasRole('Administrator') && $task->task_assign_to !== $user->user_id) {
            abort(403, 'Unauthorized access.');
        }
        
        $task->load(['project', 'client', 'status', 'assignee', 'priority', 'system', 'category', 'type']);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        if ($task->task_inactive) abort(404);
        $statusMap = $this->getStatusMapping();
        if (in_array($task->task_status_id, $statusMap['completed'])) abort(403, 'Completed tasks cannot be edited.');

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->hasRole('Administrator') && $task->task_assign_to !== $user->user_id) {
            abort(403, 'You are only allowed to edit tasks assigned to you.');
        }

        $users = User::where('user_inactive', false)->get();
        $clients = Client::where('client_inactive', false)->get();
        $projects = Project::where('project_inactive', false)->get();
        $statuses = $this->getValidStatuses();
        $priorities = Priority::where('priority_inactive', false)->get();
        $systems = System::where('system_inactive', false)->get();
        $categories = Category::where('category_inactive', false)->get();
        $types = Type::where('type_inactive', false)->get();

        return view('tasks.edit', compact('task', 'users', 'clients', 'projects', 'statuses', 'priorities', 'systems', 'categories', 'types'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $statusMap = $this->getStatusMapping();
        if (in_array($task->task_status_id, $statusMap['completed'])) abort(403, 'Completed tasks cannot be updated.');
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->hasRole('Administrator') && $task->task_assign_to !== $user->user_id) {
            abort(403, 'You are only allowed to edit tasks assigned to you.');
        }

        $isAdminOrManager = $user->hasRole('Administrator');
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

        if ($user->user_id !== $task->task_assign_to && !$user->hasRole('Administrator')) {
            abort(403, 'You cannot start a task assigned to someone else.');
        }

        $statusMap = $this->getStatusMapping();

        $task->update([
            'task_date_start' => now()->toDateString(),
            'task_time_start' => now()->toTimeString(),
            'task_status_id'  => $statusMap['progress'][0] !== -1 ? $statusMap['progress'][0] : 2, 
        ]);

        $this->logActivity('Started working on task: ' . $task->task_title);

        return back()->with('status', 'Task started! Timer is running.');
    }

    public function finish(Request $request, Task $task): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->user_id !== $task->task_assign_to && !$user->hasRole('Administrator')) {
            abort(403, 'You cannot finish a task assigned to someone else.');
        }

        $statusMap = $this->getStatusMapping();

        $updates = [
            'task_date_end' => $request->input('task_date_end') ?: now()->toDateString(),
            'task_time_end' => $request->input('task_time_end') ?: now()->toTimeString(),
            'task_status_id' => $statusMap['completed'][0] !== -1 ? $statusMap['completed'][0] : 3, 
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

        if (!$user->hasRole('Administrator')) {
            abort(403, 'Unauthorized action.');
        }
    }
}