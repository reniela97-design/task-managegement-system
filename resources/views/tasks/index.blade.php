<x-app-layout>
    {{-- Include Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar for Kanban columns */
        .kanban-col::-webkit-scrollbar { width: 6px; }
        .kanban-col::-webkit-scrollbar-track { background: transparent; }
        .kanban-col::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        
        /* Custom scrollbar for Modal Description */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 8px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }

        /* CSS to properly render TinyMCE styles inside Tailwind */
        .tinymce-content ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin-bottom: 0.5rem !important; }
        .tinymce-content ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin-bottom: 0.5rem !important; }
        .tinymce-content p { margin-bottom: 0.5rem !important; }
        .tinymce-content strong, .tinymce-content b { font-weight: bold !important; }
        .tinymce-content em, .tinymce-content i { font-style: italic !important; }
        .tinymce-content a { color: #3b82f6 !important; text-decoration: underline !important; }
    </style>

    {{-- HIDDEN DATA STORAGE --}}
    <div id="chart-data-storage" class="hidden"
         data-labels="{{ $chartLabels }}"
         data-created="{{ $chartCreated }}"
         data-completed="{{ $chartCompleted }}"
         data-bar="{{ $barData }}"
         data-pie="{{ $pieData }}">
    </div>

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                {{ __('My Tasks') }}
            </h2>
            <div class="text-sm font-medium text-gray-500">
                Active Load: <span class="text-blue-900 font-bold">{{ $activeCount }} Tasks</span>
            </div>
        </div>
    </div>

    {{-- WRAP MAIN CONTENT IN ALPINE DATA FOR BOTH MODALS --}}
    <div class="py-10" x-data="{ showModal: false, modalData: {}, showFinishModal: false, finishData: { id: null, title: '', url: '' } }">
        
        {{-- HELPER FUNCTION TO DIFF PENDING CHANGES --}}
        @php
            if (!function_exists('getPendingChanges')) {
                function getPendingChanges($task) {
                    $changes = [];
                    if ($task->task_edit_pending && $task->task_pending_data) {
                        $newData = json_decode($task->task_pending_data, true);
                        
                        // Clean labels (Removed 'ID')
                        $fields = [
                            'task_title' => 'Title', 
                            'task_description' => 'Description', 
                            'task_due_date' => 'Due Date', 
                            'task_status_id' => 'Status', 
                            'task_priority_id' => 'Priority', 
                            'task_client_id' => 'Client', 
                            'task_project_id' => 'Project', 
                            'task_assign_to' => 'Assignee',
                            'task_system_id' => 'System',
                            'task_category_id' => 'Category',
                            'task_type_id' => 'Type'
                        ];

                        foreach ($fields as $key => $label) {
                            if (array_key_exists($key, $newData) && $task->{$key} != $newData[$key]) {
                                $val = $newData[$key];
                                $displayVal = 'None/Cleared';
                                
                                if ($val !== null && $val !== '') {
                                    // Fetch human-readable names instead of raw IDs
                                    switch ($key) {
                                        case 'task_status_id':
                                            $displayVal = \App\Models\Status::find($val)->status_name ?? 'Unknown';
                                            break;
                                        case 'task_priority_id':
                                            $displayVal = \App\Models\Priority::find($val)->priority_name ?? 'Unknown';
                                            break;
                                        case 'task_client_id':
                                            $displayVal = \App\Models\Client::find($val)->client_name ?? 'Unknown';
                                            break;
                                        case 'task_project_id':
                                            $displayVal = \App\Models\Project::find($val)->project_name ?? 'Unknown';
                                            break;
                                        case 'task_assign_to':
                                            $displayVal = \App\Models\User::find($val)->user_name ?? 'Unknown';
                                            break;
                                        case 'task_system_id':
                                            $displayVal = \App\Models\System::find($val)->system_name ?? 'Unknown';
                                            break;
                                        case 'task_category_id':
                                            $displayVal = \App\Models\Category::find($val)->category_name ?? 'Unknown';
                                            break;
                                        case 'task_type_id':
                                            $displayVal = \App\Models\Type::find($val)->type_name ?? 'Unknown';
                                            break;
                                        case 'task_due_date':
                                            $displayVal = \Carbon\Carbon::parse($val)->format('M d, Y');
                                            break;
                                        case 'task_description':
                                            $clean = strip_tags($val);
                                            $displayVal = strlen($clean) > 80 ? substr($clean, 0, 80) . '...' : $clean;
                                            break;
                                        default:
                                            $displayVal = $val;
                                    }
                                }
                                $changes[$label] = $displayVal;
                            }
                        }
                    }
                    return $changes;
                }
            }
        @endphp

        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Status Messages --}}
            @if(session('status'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3 text-sm text-green-700 font-medium">{{ session('status') }}</div>
                    </div>
                </div>
            @endif

            {{-- Toolbar: Filters & Create --}}
            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-200 gap-4">
                
                {{-- Left Side: Filters --}}
                <div class="flex-1 w-full">
                    <form method="GET" action="{{ route('tasks.index') }}" class="flex flex-wrap items-center gap-4">
                        
                        {{-- User Filter (Admins/Managers only) --}}
                        @if(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager'))
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-bold text-gray-500 uppercase">Personnel:</label>
                            <select name="user_id" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 cursor-pointer bg-gray-50 py-1.5 pl-3 pr-8">
                                <option value="">{{ $viewingUser->user_name ?? 'My Tasks (Default)' }}</option>
                                <option disabled>──────────</option>
                                <option value="">-- Show All Users --</option>
                                @foreach($users as $u)
                                <option value="{{ $u->user_id }}" {{ request('user_id') == $u->user_id ? 'selected' : '' }}>{{ $u->user_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        {{-- Month Filter --}}
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-bold text-gray-500 uppercase">Month:</label>
                            <select name="filter_month" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 cursor-pointer bg-gray-50 py-1.5 pl-3 pr-8">
                                <option value="">All Months</option>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $filterMonth == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Year Filter --}}
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-bold text-gray-500 uppercase">Year:</label>
                            <select name="filter_year" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 cursor-pointer bg-gray-50 py-1.5 pl-3 pr-8">
                                <option value="">All Years</option>
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                
                {{-- Right Side: Create Button --}}
                <a href="{{ route('tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:shadow-lg transition uppercase tracking-wide text-xs flex items-center gap-2 w-full md:w-auto justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    New Task
                </a>
            </div>

            {{-- 1. NUMBER STATS DASHBOARD --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col relative overflow-hidden group hover:shadow-md transition">
                    <span class="text-gray-500 text-[10px] font-bold uppercase tracking-wider mb-1">Total Tasks</span>
                    <span class="text-3xl font-black text-gray-800">{{ $totalTasks }}</span>
                </div>
                <div class="bg-gradient-to-br from-white to-green-50/50 rounded-xl shadow-sm border border-green-100 p-5 flex flex-col relative overflow-hidden group hover:shadow-md transition">
                    <span class="text-green-600 text-[10px] font-bold uppercase tracking-wider mb-1">Completed</span>
                    <span class="text-3xl font-black text-green-700">{{ $completedCount }}</span>
                </div>
                <div class="bg-gradient-to-br from-white to-blue-50/50 rounded-xl shadow-sm border border-blue-100 p-5 flex flex-col relative overflow-hidden group hover:shadow-md transition">
                    <span class="text-blue-600 text-[10px] font-bold uppercase tracking-wider mb-1">Active Pending</span>
                    <span class="text-3xl font-black text-blue-700">{{ $activeCount }}</span>
                </div>
                <div class="bg-gradient-to-br from-white to-red-50/50 rounded-xl shadow-sm border border-red-100 p-5 flex flex-col relative overflow-hidden group hover:shadow-md transition">
                    <span class="text-red-600 text-[10px] font-bold uppercase tracking-wider mb-1">Overdue Tasks</span>
                    <span class="text-3xl font-black text-red-700">{{ $overdueCount }}</span>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-indigo-100 p-5 flex items-center justify-between group hover:shadow-md transition col-span-2 md:col-span-1 lg:col-span-1">
                    <div class="flex flex-col">
                        <span class="text-indigo-600 text-[10px] font-bold uppercase tracking-wider mb-1">Success Rate</span>
                        <span class="text-3xl font-black text-indigo-700">{{ $completionRate }}%</span>
                    </div>
                    <div class="relative w-12 h-12">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="5" fill="transparent" class="text-indigo-50" />
                            <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="5" fill="transparent" stroke-dasharray="125.6" stroke-dashoffset="{{ 125.6 - (125.6 * $completionRate / 100) }}" class="text-indigo-500 transition-all duration-1000 ease-out" stroke-linecap="round" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- 2. VISUAL CHARTS DASHBOARD --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                {{-- Line Chart: Trend Over Time --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 lg:col-span-3">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        Tasks Created vs Completed (Trend)
                    </h3>
                    <div class="relative h-64">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                {{-- Bar Chart: Completion vs Pending vs Overdue --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 lg:col-span-2">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Performance Overview
                    </h3>
                    <div class="relative h-56">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>

                {{-- Pie Chart: Status Distribution --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 lg:col-span-1">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        Status Distribution
                    </h3>
                    <div class="relative h-56 flex justify-center">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- LOGIC: SEPARATE PENDING APPROVALS FROM KANBAN COLUMNS --}}
            @php
                $allTasks = collect()->concat($pendingTasks)->concat($inProgressTasks)->concat($completedTasks);
                
                // 1. Get ONLY the tasks pending approval
                $approvalTasks = $allTasks->where('task_edit_pending', true);
                
                // 2. Get the clean columns (excluding pending approvals)
                $cleanPending = $pendingTasks->where('task_edit_pending', false);
                $cleanInProgress = $inProgressTasks->where('task_edit_pending', false);
                $cleanCompleted = $completedTasks->where('task_edit_pending', false);
            @endphp

            {{-- SEPARATED PENDING APPROVAL QUEUE --}}
            @if($approvalTasks->isNotEmpty())
            <div class="mb-8 bg-amber-50/80 border border-amber-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="bg-amber-100/60 px-6 py-4 border-b border-amber-200 flex items-center justify-between">
                    <h3 class="text-amber-900 font-black uppercase text-sm tracking-wider flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Queue: Pending Edit Approvals
                    </h3>
                    <span class="bg-amber-500 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-sm">{{ $approvalTasks->count() }} Review(s) Needed</span>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 max-h-[400px] overflow-y-auto custom-scrollbar bg-slate-50/30">
                    @foreach ($approvalTasks as $task)
                        @php
                            $taskData = [
                                'title' => $task->task_title, 'description' => $task->task_description ?? 'No description.',
                                'priority' => $task->task_priority_id == 1 ? 'Emergency' : 'Normal', 'due' => $task->task_due_date ? \Carbon\Carbon::parse($task->task_due_date)->format('M d, Y') : 'No Date',
                                'project' => $task->project->project_name ?? 'General', 'client' => $task->client->client_name ?? 'Internal',
                                'assignee' => $task->assignee->user_name ?? 'Unassigned', 'status_id' => $task->task_status_id,
                                'is_pending_approval' => (bool)$task->task_edit_pending,
                                'pending_changes' => getPendingChanges($task), // Pass new diff data here
                                'edit_url' => route('tasks.edit', $task->task_id)
                            ];
                        @endphp
                        <div class="bg-white p-4 rounded-xl border-2 border-amber-300 shadow-sm hover:shadow-md transition relative flex flex-col h-full cursor-pointer"
                             @click="modalData = {{ json_encode($taskData) }}; showModal = true;">
                            
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $task->project->project_name ?? 'General' }}</span>
                                @if($task->task_priority_id == 1)
                                    <span class="bg-red-50 text-red-600 text-[9px] font-bold px-1.5 py-0.5 rounded border border-red-100 uppercase">Emergency</span>
                                @endif
                            </div>
                            <h4 class="font-bold text-gray-800 text-sm mb-4 leading-tight flex-grow">{{ $task->task_title }}</h4>
                            
                            <div class="mt-auto pt-3 border-t border-gray-100 flex flex-col gap-2">
                                @if(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager'))
                                    <div class="flex gap-2 w-full" @click.stop>
                                        <form action="{{ route('tasks.approve', $task->task_id) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white text-[10px] font-bold py-2 rounded-lg transition uppercase tracking-widest border border-emerald-200">
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('tasks.reject', $task->task_id) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full bg-red-50 hover:bg-red-600 text-red-700 hover:text-white text-[10px] font-bold py-2 rounded-lg transition uppercase tracking-widest border border-red-200">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="block text-center w-full bg-gray-100 text-amber-600 text-[10px] font-bold py-2 rounded uppercase tracking-widest" @click.stop>
                                        Awaiting Admin
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- 3. KANBAN BOARD --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                
                {{-- COLUMN 1: PENDING (To Do) --}}
                <div class="bg-slate-50/80 border border-slate-200 rounded-2xl flex flex-col max-h-[75vh]">
                    <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-white rounded-t-2xl shadow-sm z-10">
                        <h3 class="font-black text-slate-700 uppercase tracking-wide text-sm flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> To Do
                        </h3>
                        <span class="bg-slate-100 text-slate-600 text-xs font-bold px-2.5 py-1 rounded-full">{{ $cleanPending->count() }}</span>
                    </div>
                    <div class="p-3 overflow-y-auto kanban-col flex-1 space-y-3">
                        @forelse($cleanPending as $task)
                            @php
                                $taskData = [
                                    'title' => $task->task_title, 'description' => $task->task_description ?? 'No description.',
                                    'priority' => $task->task_priority_id == 1 ? 'Emergency' : 'Normal', 'due' => $task->task_due_date ? \Carbon\Carbon::parse($task->task_due_date)->format('M d, Y') : 'No Date',
                                    'project' => $task->project->project_name ?? 'General', 'client' => $task->client->client_name ?? 'Internal',
                                    'assignee' => $task->assignee->user_name ?? 'Unassigned', 'status_id' => $task->task_status_id,
                                    'is_pending_approval' => false,
                                    'pending_changes' => getPendingChanges($task), // Pass new diff data here
                                    'edit_url' => route('tasks.edit', $task->task_id)
                                ];
                            @endphp
                            <div class="bg-white border {{ $task->task_priority_id == 1 ? 'border-red-200 border-l-4 border-l-red-500' : 'border-slate-200 border-l-4 border-l-blue-500' }} rounded-xl p-4 shadow-sm hover:shadow-md transition cursor-pointer group" @click="modalData = {{ json_encode($taskData) }}; showModal = true;">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $task->project->project_name ?? 'General' }}</span>
                                    @if($task->task_priority_id == 1)
                                        <span class="bg-red-50 text-red-600 text-[9px] font-bold px-1.5 py-0.5 rounded border border-red-100 uppercase">Emergency</span>
                                    @endif
                                </div>
                                <h4 class="font-bold text-slate-800 text-sm mb-3 leading-tight group-hover:text-blue-600 transition">{{ $task->task_title }}</h4>
                                <div class="flex justify-between items-center mt-auto border-t border-slate-50 pt-3">
                                    <div class="text-xs font-medium {{ $task->task_due_date && \Carbon\Carbon::parse($task->task_due_date)->isPast() ? 'text-red-500 font-bold' : 'text-slate-500' }} flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $task->task_due_date ? \Carbon\Carbon::parse($task->task_due_date)->format('M d') : 'No Due' }}
                                    </div>
                                    <div class="flex gap-1.5" @click.stop>
                                        <a href="{{ route('tasks.edit', $task->task_id) }}" class="bg-slate-50 text-slate-600 hover:bg-indigo-600 hover:text-white border border-slate-200 p-1.5 rounded transition" title="Edit Task">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('tasks.start', $task->task_id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white border border-emerald-200 p-1.5 rounded transition" title="Start Task">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-slate-400 text-xs font-medium">No pending tasks.</div>
                        @endforelse
                    </div>
                </div>

                {{-- COLUMN 2: IN PROGRESS --}}
                <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl flex flex-col max-h-[75vh]">
                    <div class="p-4 border-b border-indigo-100 flex justify-between items-center bg-white rounded-t-2xl shadow-sm z-10 relative overflow-hidden">
                        <div class="absolute inset-0 bg-indigo-50 opacity-50" style="background-image: linear-gradient(45deg, #e0e7ff 25%, transparent 25%, transparent 50%, #e0e7ff 50%, #e0e7ff 75%, transparent 75%, transparent); background-size: 10px 10px;"></div>
                        <h3 class="font-black text-indigo-900 uppercase tracking-wide text-sm flex items-center gap-2 relative z-10">
                            <span class="relative flex h-2.5 w-2.5">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-indigo-500"></span>
                            </span>
                            In Progress
                        </h3>
                        <span class="bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold px-2.5 py-1 rounded-full relative z-10">{{ $cleanInProgress->count() }}</span>
                    </div>
                    <div class="p-3 overflow-y-auto kanban-col flex-1 space-y-3">
                        @forelse($cleanInProgress as $task)
                            @php
                                $taskData = [
                                    'title' => $task->task_title, 'description' => $task->task_description ?? 'No description.',
                                    'priority' => $task->task_priority_id == 1 ? 'Emergency' : 'Normal', 'due' => $task->task_due_date ? \Carbon\Carbon::parse($task->task_due_date)->format('M d, Y') : 'No Date',
                                    'project' => $task->project->project_name ?? 'General', 'client' => $task->client->client_name ?? 'Internal',
                                    'assignee' => $task->assignee->user_name ?? 'Unassigned', 'status_id' => $task->task_status_id,
                                    'is_pending_approval' => false,
                                    'pending_changes' => getPendingChanges($task), // Pass new diff data here
                                    'edit_url' => route('tasks.edit', $task->task_id)
                                ];
                            @endphp
                            <div class="bg-white border-2 border-indigo-300 rounded-xl p-4 shadow-md hover:shadow-lg transition cursor-pointer group relative overflow-hidden flex flex-col min-h-[140px]" @click="modalData = {{ json_encode($taskData) }}; showModal = true;">
                                <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400">{{ $task->project->project_name ?? 'General' }}</span>
                                    <div class="flex items-center gap-2">
                                        {{-- Edit Button --}}
                                        <a href="{{ route('tasks.edit', $task->task_id) }}" @click.stop class="text-indigo-400 hover:text-indigo-700 transition" title="Edit Task">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <span class="bg-indigo-50 text-indigo-600 font-mono text-[9px] px-1.5 py-0.5 rounded border border-indigo-100 flex items-center gap-1">
                                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            {{ \Carbon\Carbon::parse($task->task_time_start)->format('h:i A') }}
                                        </span>
                                    </div>
                                </div>
                                <h4 class="font-bold text-slate-800 text-sm mb-4 leading-tight group-hover:text-indigo-700 transition">{{ $task->task_title }}</h4>
                                
                                <button @click.stop="finishData = { id: {{ $task->task_id }}, title: '{{ addslashes($task->task_title) }}', url: '{{ route('tasks.finish', $task->task_id) }}' }; showFinishModal = true;" type="button" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold py-2 rounded-lg transition uppercase tracking-widest flex items-center justify-center gap-1 mt-auto">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Mark Completed
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-10 text-indigo-300 text-xs font-medium">Nothing in progress.</div>
                        @endforelse
                    </div>
                </div>

                {{-- COLUMN 3: COMPLETED --}}
                <div class="bg-emerald-50/30 border border-emerald-100 rounded-2xl flex flex-col max-h-[75vh]">
                    <div class="p-4 border-b border-emerald-100 flex justify-between items-center bg-white rounded-t-2xl shadow-sm z-10">
                        <h3 class="font-black text-emerald-800 uppercase tracking-wide text-sm flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Done
                        </h3>
                        <span class="bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold px-2.5 py-1 rounded-full">{{ $cleanCompleted->count() }}</span>
                    </div>
                    <div class="p-3 overflow-y-auto kanban-col flex-1 space-y-3">
                        @forelse($cleanCompleted as $task)
                            @php
                                $taskData = [
                                    'title' => $task->task_title, 'description' => $task->task_description ?? 'No description.',
                                    'priority' => $task->task_priority_id == 1 ? 'Emergency' : 'Normal', 'due' => $task->task_due_date ? \Carbon\Carbon::parse($task->task_due_date)->format('M d, Y') : 'No Date',
                                    'project' => $task->project->project_name ?? 'General', 'client' => $task->client->client_name ?? 'Internal',
                                    'assignee' => $task->assignee->user_name ?? 'Unassigned', 'status_id' => $task->task_status_id,
                                    'is_pending_approval' => false,
                                    'pending_changes' => getPendingChanges($task), // Pass new diff data here
                                    'edit_url' => route('tasks.edit', $task->task_id)
                                ];
                            @endphp
                            <div class="bg-white border border-emerald-100 rounded-xl p-3 shadow-sm hover:shadow-md transition cursor-pointer group opacity-80 hover:opacity-100 flex justify-between items-center" @click="modalData = {{ json_encode($taskData) }}; showModal = true;">
                                <div class="flex items-start gap-3 w-full pr-2">
                                    <div class="mt-1 h-5 w-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-slate-700 text-xs line-through truncate">{{ $task->task_title }}</h4>
                                        <div class="text-[9px] text-slate-400 mt-1 uppercase tracking-wider font-bold">
                                            Finished: {{ $task->task_date_end ? \Carbon\Carbon::parse($task->task_date_end)->format('M d, Y') : '' }}
                                        </div>
                                    </div>
                                </div>
                                {{-- Edit Button --}}
                                <a href="{{ route('tasks.edit', $task->task_id) }}" @click.stop class="text-slate-300 hover:text-indigo-600 transition flex-shrink-0 p-1" title="Edit Task">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-10 text-emerald-400 text-xs font-medium">No completed tasks in this period.</div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- UNASSIGNED POOL --}}
            @if(isset($unassignedTasks) && $unassignedTasks->count() > 0)
            <div class="mt-12 bg-amber-50 border border-amber-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="bg-amber-100/50 px-6 py-4 border-b border-amber-200 flex items-center justify-between">
                    <h3 class="text-amber-900 font-black uppercase text-sm tracking-wider flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Unclaimed / Unassigned Pool
                    </h3>
                    <span class="bg-amber-500 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-sm">{{ $unassignedTasks->count() }} Available</span>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($unassignedTasks as $task)
                        @php
                            $taskData = [
                                'title' => $task->task_title, 'description' => $task->task_description ?? 'No description.',
                                'priority' => $task->task_priority_id == 1 ? 'Emergency' : 'Normal', 'due' => $task->task_due_date ? \Carbon\Carbon::parse($task->task_due_date)->format('M d, Y') : 'No Date',
                                'project' => $task->project->project_name ?? 'General', 'client' => $task->client->client_name ?? 'Internal',
                                'assignee' => 'Unassigned', 'status_id' => $task->task_status_id,
                                'is_pending_approval' => false,
                                'pending_changes' => getPendingChanges($task), // Pass new diff data here
                                'edit_url' => route('tasks.edit', $task->task_id)
                            ];
                        @endphp
                        <div class="bg-white p-4 rounded-xl border border-amber-200 shadow-sm hover:shadow-md transition relative group flex flex-col h-full cursor-pointer"
                             @click="modalData = {{ json_encode($taskData) }}; showModal = true;">
                            <div class="flex justify-between items-start mb-2">
                                <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded border {{ $task->task_priority_id == 1 ? 'bg-red-50 text-red-700 border-red-100' : 'bg-blue-50 text-blue-700 border-blue-100' }}">
                                    {{ $task->task_priority_id == 1 ? 'Emergency' : 'Normal' }}
                                </span>
                            </div>
                            <h4 class="font-bold text-gray-800 text-sm mb-2 leading-tight">{{ $task->task_title }}</h4>
                            
                            {{-- Strip tags here so raw HTML doesn't show in the card preview --}}
                            <p class="text-[10px] text-gray-500 mb-4 line-clamp-2 flex-grow">
                                {{ strip_tags($task->task_description) ?: 'No details.' }}
                            </p>
                            
                            <div class="mt-auto pt-3 border-t border-gray-100">
                                @if(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager'))
                                    <a href="{{ route('tasks.edit', $task->task_id) }}" @click.stop class="block text-center w-full bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-bold py-2 rounded transition uppercase tracking-widest shadow-sm">
                                        Assign Task
                                    </a>
                                @else
                                    <div class="block text-center w-full bg-gray-100 text-gray-400 text-[10px] font-bold py-2 rounded uppercase tracking-widest" @click.stop>
                                        Pending Admin
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- 1. TASK DETAILS MODAL (Pop-up) --}}
        <div x-show="showModal" x-cloak class="relative z-[50]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                 @click="showModal = false"></div>

            <div class="fixed inset-0 z-[51] w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showModal"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-200 flex flex-col max-h-[90vh]">
                        
                        <div class="px-6 py-4 flex justify-between items-center border-b border-gray-100 bg-gray-50/50">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg shadow-sm" :class="modalData.priority === 'Emergency' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </div>
                                <h3 class="text-lg font-black text-gray-800 uppercase tracking-wide">Task Information</h3>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 text-[10px] font-bold uppercase rounded-full border shadow-sm"
                                      :class="modalData.priority === 'Emergency' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200'"
                                      x-text="modalData.priority">
                                </span>
                                <button @click="showModal = false" class="text-gray-400 hover:text-gray-700 hover:bg-gray-200 transition rounded-full p-1.5 focus:outline-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>

                        <div class="px-6 py-6 overflow-y-auto flex-1 custom-scrollbar">
                            
                            {{-- Edit Pending Notice WITH DATA DIFF --}}
                            <template x-if="modalData.is_pending_approval">
                                <div class="mb-6 bg-amber-50/80 border border-amber-200 rounded-xl overflow-hidden shadow-sm">
                                    <div class="bg-amber-100/50 px-4 py-3 border-b border-amber-200 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <span class="text-xs font-black text-amber-800 uppercase tracking-widest">Pending Edit Request</span>
                                    </div>
                                    <div class="p-4">
                                        <p class="text-amber-700 text-xs mb-4 font-medium">The following modifications are awaiting manager/admin approval:</p>
                                        
                                        <div class="bg-white border border-amber-100 rounded-lg p-1 shadow-sm">
                                            <ul class="divide-y divide-amber-50">
                                                <template x-for="(value, key) in modalData.pending_changes" :key="key">
                                                    <li class="px-3 py-2 flex flex-col sm:flex-row sm:items-center gap-2 hover:bg-amber-50/30 transition">
                                                        <span class="font-black text-slate-400 uppercase tracking-widest text-[9px] w-28 shrink-0" x-text="key"></span>
                                                        <span class="text-sm font-bold text-amber-900 break-words" x-text="value"></span>
                                                    </li>
                                                </template>
                                                <template x-if="Object.keys(modalData.pending_changes || {}).length === 0">
                                                    <li class="px-3 py-2 text-xs text-amber-600/70 italic font-medium">No recognizable fields were changed.</li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div class="mb-6">
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Task Title
                                </h4>
                                <p class="text-xl font-bold text-gray-900 leading-tight" x-text="modalData.title"></p>
                            </div>

                            <div class="mb-6">
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                    Description
                                </h4>
                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 shadow-inner max-h-48 overflow-y-auto custom-scrollbar">
                                    <div class="text-sm text-slate-700 leading-relaxed tinymce-content" x-html="modalData.description"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-y-6 gap-x-4 border-t border-gray-100 pt-6">
                                <div>
                                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Client</h4>
                                    <p class="text-sm font-bold text-gray-800 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        <span x-text="modalData.client"></span>
                                    </p>
                                </div>
                                <div>
                                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Project</h4>
                                    <p class="text-sm font-bold text-gray-800 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                        <span x-text="modalData.project"></span>
                                    </p>
                                </div>
                                <div>
                                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Due Date</h4>
                                    <p class="text-sm font-bold flex items-center gap-1.5" :class="modalData.due === 'No Date' ? 'text-gray-500' : 'text-red-600'">
                                        <svg class="w-4 h-4" :class="modalData.due === 'No Date' ? 'text-gray-400' : 'text-red-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span x-text="modalData.due"></span>
                                    </p>
                                </div>
                                <div>
                                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5" x-text="modalData.status_id == 3 ? 'Completed By' : 'Assigned To'"></h4>
                                    <p class="text-sm font-bold flex items-center gap-1.5" :class="modalData.status_id == 3 ? 'text-emerald-600' : 'text-gray-800'">
                                        <svg x-show="modalData.status_id != 3" class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <svg x-show="modalData.status_id == 3" class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span x-text="modalData.assignee"></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50/80 px-6 py-4 flex justify-end gap-3 border-t border-gray-100 rounded-b-2xl">
                            <a :href="modalData.edit_url" class="inline-flex justify-center items-center gap-1.5 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm border border-indigo-700 hover:bg-indigo-700 transition-all w-auto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit Task
                            </a>
                            <button @click="showModal = false" type="button" class="inline-flex justify-center rounded-xl bg-white px-6 py-2.5 text-sm font-bold text-gray-700 shadow-sm border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition-all w-auto">
                                Close Window
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. TASK FINISH MODAL (Date/Time Confirmation) --}}
        <div x-show="showFinishModal" x-cloak class="relative z-[60]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showFinishModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                 @click="showFinishModal = false"></div>

            <div class="fixed inset-0 z-[61] w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showFinishModal"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-200 flex flex-col">
                        
                        <form :action="finishData.url" method="POST">
                            @csrf
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="text-lg font-black text-emerald-800 uppercase tracking-wide flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Confirm Task Completion
                                </h3>
                                <p class="text-xs text-gray-500 font-bold mt-1 line-clamp-1" x-text="finishData.title"></p>
                            </div>
                            
                            <div class="p-6 space-y-4">
                                <p class="text-[11px] text-gray-500 uppercase tracking-wide font-bold mb-4 border-b pb-2">Set Completion Timestamp</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Date Finished</label>
                                        <input type="date" name="task_date_end" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium" value="{{ now()->toDateString() }}" required>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Time Finished</label>
                                        <input type="time" name="task_time_end" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium" value="{{ now()->format('H:i') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50/80 px-6 py-4 flex justify-end gap-3 border-t border-gray-100 rounded-b-2xl">
                                <button @click="showFinishModal = false" type="button" class="inline-flex justify-center rounded-xl bg-white px-5 py-2 text-xs font-bold text-gray-600 shadow-sm border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition-all">
                                    Cancel
                                </button>
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white shadow-md border border-emerald-700 hover:bg-emerald-700 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Save Completion
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- CLEAN CHART.JS INITIALIZATION --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // Safely fetch data from DOM to prevent VS Code Errors
            const dataStorage = document.getElementById('chart-data-storage');
            const chartLabels = JSON.parse(dataStorage.getAttribute('data-labels') || '[]');
            const chartCreated = JSON.parse(dataStorage.getAttribute('data-created') || '[]');
            const chartCompleted = JSON.parse(dataStorage.getAttribute('data-completed') || '[]');
            const barData = JSON.parse(dataStorage.getAttribute('data-bar') || '[]');
            const pieData = JSON.parse(dataStorage.getAttribute('data-pie') || '[]');

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, font: { family: "'Instrument Sans', sans-serif", size: 12 } }
                    }
                }
            };

            const trendCtx = document.getElementById('trendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Tasks Created',
                            data: chartCreated,
                            borderColor: '#6366f1', 
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            borderWidth: 2,
                            pointBackgroundColor: '#6366f1',
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Tasks Completed',
                            data: chartCompleted,
                            borderColor: '#10b981', 
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            pointBackgroundColor: '#10b981',
                            fill: true,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } },
                        x: { grid: { display: false } }
                    }
                }
            });

            const barCtx = document.getElementById('barChart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: ['Completed', 'Active Pending', 'Overdue'],
                    datasets: [{
                        label: 'Tasks',
                        data: barData,
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.8)', 
                            'rgba(59, 130, 246, 0.8)', 
                            'rgba(239, 68, 68, 0.8)'   
                        ],
                        borderRadius: 6,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } },
                        x: { grid: { display: false } }
                    }
                }
            });

            const pieCtx = document.getElementById('pieChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'In Progress', 'Completed'],
                    datasets: [{
                        data: pieData,
                        backgroundColor: [
                            '#e2e8f0', 
                            '#818cf8', 
                            '#34d399'  
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    ...commonOptions,
                    cutout: '65%' 
                }
            });
        });
    </script>
</x-app-layout>