<x-app-layout>
    {{-- Include Chart.js & FullCalendar --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

    @php
        // Get the total unread count for the notification ping dot
        $unreadCount = auth()->user()->unreadNotifications->count();
    @endphp

    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbars */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 8px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }

        .tinymce-content ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin-bottom: 0.5rem !important; }
        .tinymce-content ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin-bottom: 0.5rem !important; }
        .tinymce-content p { margin-bottom: 0.5rem !important; }
        .tinymce-content strong, .tinymce-content b { font-weight: bold !important; }
        .tinymce-content em, .tinymce-content i { font-style: italic !important; }
        .tinymce-content a { color: #3b82f6 !important; text-decoration: underline !important; }

        /* Notification Bell Animation */
        @keyframes ring {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(15deg); }
            50% { transform: rotate(-10deg); }
            75% { transform: rotate(5deg); }
        }
        .animate-ring { 
            animation: ring 1.5s ease-in-out infinite; 
            transform-origin: top center; 
        }
    </style>

    {{-- HIDDEN DATA STORAGE --}}
    <div id="dashboard-data" class="hidden"
         data-not-started="{{ $notStartedCount }}"
         data-in-progress="{{ $inProgressCount }}"
         data-completed="{{ $completedTasks }}"
         data-events="{{ json_encode($events) }}">
    </div>

    {{-- SYSTEM HEADER WITH MINIMALIST NOTIFICATION BELL --}}
    <div class="bg-white border-b border-gray-200 shadow-sm relative z-50">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            
            <h2 class="font-black text-2xl text-blue-900 uppercase tracking-tight">
                System Overview
            </h2>

            <div class="flex items-center gap-6">
                {{-- Status Badge --}}
                <div class="hidden sm:flex text-[10px] font-bold text-gray-500 uppercase tracking-widest bg-gray-100 px-3 py-1.5 rounded-full items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></span>
                    Operational
                </div>

                {{-- Minimalist Notification Bell Dropdown --}}
                <div x-data="{ openNotifs: false }" class="relative">
                    <button @click="openNotifs = !openNotifs" @click.away="openNotifs = false" class="relative p-2 text-gray-400 hover:text-blue-600 transition-colors focus:outline-none rounded-full hover:bg-gray-50">
                        <svg class="w-6 h-6 {{ $unreadCount > 0 ? 'animate-ring text-blue-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        
                        {{-- Pinging Red Dot for New Notifications --}}
                        @if($unreadCount > 0)
                            <span class="absolute top-1 right-1 flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 border-2 border-white"></span>
                            </span>
                        @endif
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="openNotifs" x-transition x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Alerts</span>
                            @if($unreadCount > 0)
                                <form action="{{ route('notifications.readAll') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-blue-600 hover:text-blue-800 font-bold uppercase tracking-wider">Mark Read</button>
                                </form>
                            @endif
                        </div>
                        
                        <div class="max-h-64 overflow-y-auto custom-scrollbar">
                            @if(!isset($notifications) || $notifications->isEmpty())
                                <div class="p-6 text-center text-xs font-medium text-gray-400">
                                    <svg class="w-8 h-8 mx-auto text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                                    All caught up!
                                </div>
                            @else
                                <ul class="divide-y divide-gray-50">
                                    @foreach($notifications as $n)
                                        <li>
                                            <a href="{{ route('notifications.read', $n->id) }}" class="flex items-start gap-3 p-4 hover:bg-blue-50/50 transition-colors group">
                                                <div class="w-2 h-2 mt-1.5 rounded-full bg-blue-500 shrink-0 group-hover:scale-125 transition-transform"></div>
                                                <div>
                                                    <p class="text-xs font-bold text-gray-800 group-hover:text-blue-700 transition-colors">{{ $n->data['title'] ?? 'Alert' }}</p>
                                                    <p class="text-[10px] text-gray-500 mt-0.5 line-clamp-1">{{ $n->data['message'] }}</p>
                                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1.5">{{ $n->created_at->diffForHumans() }}</p>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        
                        {{-- Redirect Button --}}
                        <a href="{{ route('notifications.index') }}" class="block w-full py-3 text-center bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest transition-colors">
                            Open Notification Center
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-8" x-data="{ showModal: false, modalData: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- GLOBAL DASHBOARD FILTERS --}}
            <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row items-center justify-between shadow-sm gap-4">
                <span class="text-gray-800 font-bold uppercase text-xs tracking-wider flex items-center gap-2 whitespace-nowrap"> 
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Dashboard Filters
                </span>
                
                <form method="GET" action="{{ route('dashboard') }}" class="w-full flex flex-col md:flex-row justify-end items-center gap-3">
                    
                    {{-- Month Filter --}}
                    <select name="filter_month" class="w-full md:w-auto text-sm rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900 shadow-sm bg-gray-50" onchange="this.form.submit()">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ request('filter_month') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Year Filter --}}
                    <select name="filter_year" class="w-full md:w-auto text-sm rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900 shadow-sm bg-gray-50" onchange="this.form.submit()">
                        <option value="">All Years</option>
                        @foreach(range(2023, now()->addYear()->year) as $y)
                            <option value="{{ $y }}" {{ request('filter_year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>

                    {{-- Personnel Filter (Admin/Manager Only) --}}
                    @if(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager'))
                        <select name="user_id" class="w-full md:w-56 text-sm rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900 shadow-sm bg-gray-50" onchange="this.form.submit()">
                            <option value="">-- All Personnel --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->user_id }}" {{ request('user_id') == $u->user_id ? 'selected' : '' }}>
                                    {{ $u->user_name }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                </form>
            </div>

            {{-- STATS GRID --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-900 overflow-hidden shadow-lg rounded-xl relative group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition">
                        <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="p-6">
                        <div class="text-blue-200 text-xs font-bold uppercase tracking-widest">Pending Tasks</div>
                        <div class="text-5xl font-black text-white mt-1">{{ $totalPending }}</div>
                        <div class="mt-4"><span class="text-blue-100 text-xs">Active Assignments</span></div>
                    </div>
                </div>

                <div class="bg-red-900 overflow-hidden shadow-lg rounded-xl relative group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition">
                        <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div class="p-6">
                        <div class="text-red-200 text-xs font-bold uppercase tracking-widest">Emergency Priority</div>
                        <div class="text-5xl font-black text-white mt-1">{{ $highPriorityCount }}</div>
                        <div class="mt-4"><span class="text-red-200 text-xs">Requires Immediate Attention</span></div>
                    </div>
                </div>

                <div class="bg-green-700 overflow-hidden shadow-lg rounded-xl relative group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition">
                        <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="p-6">
                        <div class="text-green-200 text-xs font-bold uppercase tracking-widest">Tasks Completed</div>
                        <div class="text-5xl font-black text-white mt-1">{{ $completedTasks }}</div>
                        <div class="mt-4"><span class="text-green-100 text-xs">Successfully Finished</span></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- MAIN GRAPH: TASK ANALYTICS --}}
                <div class="lg:col-span-2 bg-white shadow-lg rounded-xl border border-gray-100 p-6">
                    <div class="flex justify-between items-end mb-6">
                        <h3 class="font-bold text-gray-800 text-lg">Task Volume Analytics</h3>
                        <span class="text-xs text-gray-500 font-medium">Distribution by Status</span>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="taskBarChart"></canvas>
                    </div>
                </div>

                {{-- MINI ANALYTICS: COMPLETION RATE --}}
                <div class="bg-white shadow-lg rounded-xl border border-gray-100 p-6 flex flex-col justify-between">
                    <div class="flex justify-between items-end mb-4">
                        <h3 class="font-bold text-gray-800 text-lg">Completion Rate</h3>
                        <span class="text-xs text-gray-500 font-medium">Performance</span>
                    </div>
                    <div class="flex items-center justify-center">
                        <div class="relative w-40 h-40">
                            <svg class="w-full h-full transform -rotate-90">
                                <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="transparent" class="text-gray-50" />
                                <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="transparent" 
                                    stroke-dasharray="439.8" 
                                    stroke-dashoffset="{{ 439.8 - (439.8 * $completionPercentage / 100) }}" 
                                    class="text-green-500 transition-all duration-1000 ease-out" />
                            </svg>
                            <div class="absolute top-0 left-0 w-full h-full flex flex-col items-center justify-center">
                                <span class="text-4xl font-black text-green-600">{{ $completionPercentage }}%</span>
                                <span class="text-[10px] text-gray-400 uppercase font-bold mt-1">Done</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-500">
                            Completed <strong class="text-gray-800">{{ $completedTasks }}</strong> of <strong class="text-gray-800">{{ $totalTasks }}</strong> total tasks.
                        </p>
                    </div>
                </div>
            </div>

            {{-- CALENDAR & LIST ROW --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Mini Calendar --}}
                <div class="bg-white shadow-lg rounded-xl border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-800 text-lg">Quick Calendar</h3>
                        <a href="{{ route('reports.calendar') }}" class="text-[10px] font-bold text-blue-600 uppercase hover:underline">Full View →</a>
                    </div>
                    <div id="mini-calendar" class="text-xs relative z-0"></div>
                </div>

                {{-- Pending Task List --}}
                <div class="lg:col-span-2 bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100 flex flex-col max-h-[400px]">
                    <div class="bg-blue-900 px-6 py-4 border-b border-blue-800 flex justify-between items-center shrink-0">
                        <h3 class="text-white font-bold uppercase text-sm tracking-wide">Pending Deadlines</h3>
                        <span class="bg-blue-800 text-blue-200 py-1 px-3 rounded text-xs font-bold">{{ $upcomingDue->count() }} Records</span>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto custom-scrollbar bg-white">
                        @if($upcomingDue->isEmpty())
                            <div class="p-8 text-center text-gray-400 h-full flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                No pending tasks found.
                            </div>
                        @else
                            <table class="w-full text-left border-collapse">
                                <thead class="sticky top-0 bg-gray-50 border-b border-gray-200 shadow-sm z-10">
                                    <tr class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">
                                        <th class="px-6 py-3">Task Title</th>
                                        <th class="px-6 py-3">Priority</th>
                                        <th class="px-6 py-3">Due Date</th>
                                        <th class="px-6 py-3 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($upcomingDue as $task)
                                    @php
                                        $taskData = [
                                            'title' => $task->task_title, 
                                            'description' => $task->task_description ?? 'No description.',
                                            'priority' => $task->task_priority_id == 1 ? 'Emergency' : 'Normal', 
                                            'due' => $task->task_due_date ? \Carbon\Carbon::parse($task->task_due_date)->format('M d, Y') : 'No Date',
                                            'project' => $task->project->project_name ?? 'General', 
                                            'client' => $task->client->client_name ?? 'Internal',
                                            'assignee' => $task->assignee->user_name ?? 'Unassigned', 
                                            'status_id' => $task->task_status_id,
                                            'is_pending_approval' => (bool)$task->task_edit_pending,
                                            'edit_url' => route('tasks.edit', $task->task_id)
                                        ];
                                    @endphp
                                    <tr class="hover:bg-blue-50 transition group cursor-pointer" @click="modalData = {{ json_encode($taskData) }}; showModal = true;">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800 text-sm">{{ $task->task_title }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded text-[10px] font-bold border tracking-widest uppercase
                                                {{ $task->task_priority_id == 1 ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                                {{ $task->task_priority_id == 1 ? 'Emergency' : 'Normal' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-bold text-gray-600">
                                            {{ \Carbon\Carbon::parse($task->task_due_date)->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('tasks.show', $task->task_id) }}" class="inline-flex items-center gap-1 bg-white border border-gray-200 hover:border-blue-300 text-gray-600 hover:text-blue-700 font-bold text-[10px] uppercase tracking-wider px-3 py-1.5 rounded-lg shadow-sm transition" @click.stop>
                                                Open <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- TASK DETAILS MODAL (Pop-up) --}}
        <div x-show="showModal" x-cloak class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                 @click="showModal = false"></div>

            <div class="fixed inset-0 z-[101] w-screen overflow-y-auto">
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
                            <template x-if="modalData.is_pending_approval">
                                <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700 font-bold flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Note: Displaying current task. Changes are pending approval.
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

    </div>

    {{-- CLEAN JAVASCRIPT (No Blade Syntax to break VS Code) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // SAFELY LOAD DATA FROM DOM
            const dashboardData = document.getElementById('dashboard-data');
            const notStarted = parseInt(dashboardData.getAttribute('data-not-started')) || 0;
            const inProgress = parseInt(dashboardData.getAttribute('data-in-progress')) || 0;
            const completed = parseInt(dashboardData.getAttribute('data-completed')) || 0;
            const eventsData = JSON.parse(dashboardData.getAttribute('data-events') || '[]');

            // 1. BAR CHART CONFIGURATION
            const ctx = document.getElementById('taskBarChart');
            if(ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['Not Started', 'In Progress', 'Completed'],
                        datasets: [{
                            label: 'Tasks',
                            data: [notStarted, inProgress, completed],
                            backgroundColor: [
                                'rgba(148, 163, 184, 0.7)', 
                                'rgba(37, 99, 235, 0.7)',   
                                'rgba(22, 163, 74, 0.7)'    
                            ],
                            borderColor: [
                                'rgb(148, 163, 184)',
                                'rgb(37, 99, 235)',
                                'rgb(22, 163, 74)'
                            ],
                            borderWidth: 1,
                            borderRadius: 6,
                            barPercentage: 0.6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(30, 58, 138, 0.9)',
                                titleFont: { family: "'Figtree', sans-serif", size: 13 },
                                bodyFont: { family: "'Figtree', sans-serif", size: 12 },
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0, 0, 0, 0.05)' },
                                ticks: { font: { family: "'Figtree', sans-serif" } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: "'Figtree', sans-serif", weight: 'bold' } }
                            }
                        }
                    }
                });
            }

            // 2. MINI CALENDAR CONFIGURATION
            var calendarEl = document.getElementById('mini-calendar');
            if (calendarEl) {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev',
                        center: 'title',
                        right: 'next'
                    },
                    height: 300,
                    events: eventsData,
                    eventOrder: 'priority_sort',
                    displayEventTime: false,
                    
                    eventClick: function(info) {
                        if (info.event.url) {
                            window.location.href = info.event.url;
                            info.jsEvent.preventDefault();
                        }
                    },
                    eventDidMount: function(info) {
                        info.el.title = info.event.title;
                    }
                });
                calendar.render();
            }
        });
    </script>
    <style>
        /* Mini Calendar Tweaks */
        #mini-calendar .fc-toolbar-title { font-size: 1rem !important; color: #1e3a8a; text-transform: uppercase; font-weight: 800; }
        #mini-calendar .fc-button { padding: 2px 6px !important; font-size: 0.7rem !important; background: #1e3a8a; border: none; }
        #mini-calendar .fc-daygrid-day-number { font-size: 0.75rem; padding: 2px; color: #475569; }
        #mini-calendar .fc-event { font-size: 0.65rem; padding: 1px 2px; border-radius: 2px; }
        #mini-calendar .fc-day-today { background-color: #eff6ff !important; }
    </style>
</x-app-layout>