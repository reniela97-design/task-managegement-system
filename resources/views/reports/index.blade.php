<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbars and TinyMCE styles for the Modal */
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

        /* Print formatting (Hides filters, navigation, and sets width) */
        @media print {
            body { background-color: #fff !important; color: #000 !important; }
            .no-print, nav, header, form { display: none !important; }
            .print-w-full { max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
            .shadow-sm, .shadow-xl, .shadow-md { box-shadow: none !important; }
            .border { border-color: #e5e7eb !important; }
            .bg-emerald-50, .bg-amber-50, .bg-red-50 { background-color: transparent !important; }
            table { break-inside: auto; width: 100% !important; }
            tr { break-inside: avoid; page-break-after: auto; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>

    {{-- PAGE HEADER --}}
    <div class="bg-white border-b border-gray-200 shadow-sm no-print">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-xl shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl text-gray-900 uppercase tracking-tight">
                        Performance Reports
                    </h2>
                    <p class="text-sm font-medium text-gray-500">Analytics, Productivity, and Standing Data Overview</p>
                </div>
            </div>

            {{-- EXPORT AND PRINT BUTTONS --}}
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-indigo-700 font-bold py-2.5 px-5 rounded-xl shadow-sm transition text-[10px] uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print Report
                </button>
                <button onclick="exportToCSV('Performance_Report.csv')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-sm transition text-[10px] uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download CSV
                </button>
            </div>
        </div>
    </div>

    {{-- Wrap main content in x-data for Modal support --}}
    <div class="py-8 print-w-full" x-data="{ showModal: false, modalData: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 print-w-full">

            {{-- 1. KPI SUMMARY CARDS --}}
            @php
                $prodCount = count($productivityTasks ?? []);
                $agingCount = count($agingTasks ?? []);
                $criticalCount = collect($agingTasks ?? [])->where('aging_days', '>', 7)->count();
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center justify-between group hover:shadow-md transition">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Completed Tasks</p>
                        <p class="text-3xl font-black text-emerald-600">{{ $prodCount }}</p>
                    </div>
                    <div class="p-3 bg-emerald-50 text-emerald-500 rounded-xl group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center justify-between group hover:shadow-md transition">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Active Pending</p>
                        <p class="text-3xl font-black text-amber-500">{{ $agingCount }}</p>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-500 rounded-xl group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center justify-between group hover:shadow-md transition">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Critical Aging (>7 Days)</p>
                        <p class="text-3xl font-black text-red-600">{{ $criticalCount }}</p>
                    </div>
                    <div class="p-3 bg-red-50 text-red-500 rounded-xl group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
            </div>
            
            {{-- 2. CONTROL PANEL (FILTERS) --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm relative overflow-hidden no-print">
                <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-5 gap-4">
                    <h3 class="text-indigo-900 font-bold uppercase text-xs tracking-widest flex items-center gap-2"> 
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Data Parameters & Filters
                    </h3>
                    <a href="{{ route('reports.index') }}" class="text-[10px] font-bold text-gray-500 hover:text-red-600 uppercase tracking-widest transition bg-gray-50 hover:bg-red-50 border border-gray-200 hover:border-red-200 px-4 py-2 rounded-lg flex items-center gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Reset All
                    </a>
                </div>
                
                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 w-full">
                    
                    {{-- Dates --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Month</label>
                        <select name="filter_month" class="w-full text-sm rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50" onchange="this.form.submit()">
                            <option value="">All Months</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ request('filter_month') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Year</label>
                        <select name="filter_year" class="w-full text-sm rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50" onchange="this.form.submit()">
                            <option value="">All Years</option>
                            @foreach(range(2023, now()->addYear()->year) as $y)
                                <option value="{{ $y }}" {{ request('filter_year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Personnel --}}
                    @if(Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Manager'))
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Personnel</label>
                            <select name="user_id" class="w-full text-sm rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50" onchange="this.form.submit()">
                                <option value="">All Personnel</option>
                                @foreach($users ?? [] as $u)
                                    <option value="{{ $u->user_id }}" {{ request('user_id') == $u->user_id ? 'selected' : '' }}>{{ $u->user_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Client --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Client</label>
                        <select name="client_id" class="w-full text-sm rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50" onchange="this.form.submit()">
                            <option value="">All Clients</option>
                            @foreach($clients ?? [] as $client)
                                <option value="{{ $client->client_id }}" {{ request('client_id') == $client->client_id ? 'selected' : '' }}>{{ $client->client_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Project --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Project</label>
                        <select name="project_id" class="w-full text-sm rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50" onchange="this.form.submit()">
                            <option value="">All Projects</option>
                            @foreach($projects ?? [] as $project)
                                <option value="{{ $project->project_id }}" {{ request('project_id') == $project->project_id ? 'selected' : '' }}>{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Category</label>
                        <select name="category_id" class="w-full text-sm rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->category_id }}" {{ request('category_id') == $category->category_id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- System --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">System</label>
                        <select name="system_id" class="w-full text-sm rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50" onchange="this.form.submit()">
                            <option value="">All Systems</option>
                            @foreach($systems ?? [] as $system)
                                <option value="{{ $system->system_id }}" {{ request('system_id') == $system->system_id ? 'selected' : '' }}>{{ $system->system_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Task Type</label>
                        <select name="type_id" class="w-full text-sm rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            @foreach($types ?? [] as $type)
                                <option value="{{ $type->type_id }}" {{ request('type_id') == $type->type_id ? 'selected' : '' }}>{{ $type->type_name }}</option>
                            @endforeach
                        </select>
                    </div>

                </form>
            </div>

            {{-- 3. PRODUCTIVITY TABLE (Completed Data) --}}
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-2xl overflow-hidden mt-8">
                <div class="bg-emerald-50/50 px-6 py-5 border-b border-emerald-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                    <div>
                        <h3 class="text-emerald-900 font-bold uppercase tracking-widest text-sm flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Productivity Ledger
                        </h3>
                        <p class="text-xs text-emerald-700/70 mt-1 font-medium no-print">Completed tasks with recorded execution times.</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table id="productivity-table" class="min-w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-white text-gray-400 font-bold uppercase text-[10px] tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Task Details</th>
                                <th class="px-6 py-4">Assignee</th>
                                <th class="px-6 py-4 text-center">Timeliness</th>
                                <th class="px-6 py-4 text-right">Total Duration</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($productivityTasks ?? [] as $task)
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
                            <tr class="hover:bg-blue-50/50 transition cursor-pointer group" @click="modalData = {{ json_encode($taskData) }}; showModal = true;">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 text-sm group-hover:text-blue-700 transition">{{ $task->task_title }}</div>
                                    <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest flex items-center gap-1 group-hover:text-blue-500 transition">
                                        <svg class="w-3 h-3 no-print" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ \Carbon\Carbon::parse($task->task_date_end)->format('M d, Y') }} 
                                        <span class="text-gray-300">|</span> 
                                        {{ $task->task_time_end ? \Carbon\Carbon::parse($task->task_time_end)->format('h:i A') : '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-7 w-7 rounded-full bg-gradient-to-tr from-emerald-100 to-emerald-200 text-emerald-700 flex items-center justify-center text-xs font-black shadow-sm border border-emerald-300/50 no-print">
                                            {{ substr($task->assignee->user_name ?? 'U', 0, 1) }}
                                        </div>
                                        <span class="font-bold text-gray-700 text-xs">{{ $task->assignee->user_name ?? 'Unassigned' }}</span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    @if($task->task_due_date)
                                        @php
                                            $finished = \Carbon\Carbon::parse($task->task_date_end);
                                            $due = \Carbon\Carbon::parse($task->task_due_date)->endOfDay(); 
                                            $isOverdue = $finished->gt($due);
                                        @endphp

                                        @if($isOverdue)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-black bg-red-50 text-red-600 border border-red-200 uppercase tracking-widest shadow-sm">
                                                Overdue
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-black bg-emerald-50 text-emerald-600 border border-emerald-200 uppercase tracking-widest shadow-sm">
                                                On Time
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-[10px] text-gray-400 font-medium italic">No Target</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right font-black text-emerald-600 text-base font-mono">
                                    {{ $task->duration_string ?? 'N/A' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <div class="bg-gray-50 rounded-full h-16 w-16 flex items-center justify-center mx-auto mb-4 border border-gray-100 shadow-sm">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                    </div>
                                    <p class="text-gray-900 font-bold text-sm">No productivity records</p>
                                    <p class="text-gray-500 text-xs mt-1">Try adjusting your filters to find completed tasks.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 4. STANDING DATA (Aging / Outstanding Tasks) --}}
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-2xl overflow-hidden mt-8">
                <div class="bg-amber-50/50 px-6 py-5 border-b border-amber-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                    <div>
                        <h3 class="text-amber-900 font-bold uppercase tracking-widest text-sm flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Standing Data (Aging)
                        </h3>
                        <p class="text-xs text-amber-700/70 mt-1 font-medium no-print">Currently active tasks calculating days open.</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table id="aging-table" class="min-w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-white text-gray-400 font-bold uppercase text-[10px] tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Task Details</th>
                                <th class="px-6 py-4">Assignee</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Days Active</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($agingTasks ?? [] as $task)
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
                            <tr class="hover:bg-blue-50/50 transition cursor-pointer group" @click="modalData = {{ json_encode($taskData) }}; showModal = true;">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 text-sm group-hover:text-blue-700 transition">{{ $task->task_title }}</div>
                                    <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest flex items-center gap-1 group-hover:text-blue-500 transition">
                                        <svg class="w-3 h-3 no-print" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Logged: {{ \Carbon\Carbon::parse($task->task_log_datetime)->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-7 w-7 rounded-full bg-gradient-to-tr from-amber-100 to-amber-200 text-amber-700 flex items-center justify-center text-xs font-black shadow-sm border border-amber-300/50 no-print">
                                            {{ substr($task->assignee->user_name ?? 'U', 0, 1) }}
                                        </div>
                                        <span class="font-bold text-gray-700 text-xs">{{ $task->assignee->user_name ?? 'Unassigned' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{-- FIX: Using {!! !!} avoids the VS Code CSS property value error! --}}
                                    <span class="px-2.5 py-1 rounded-md text-[9px] font-black text-white shadow-sm tracking-widest uppercase print:text-black print:border print:border-gray-300" 
                                          {!! 'style="background-color: ' . ($task->status?->status_color ?? '#999999') . ';"' !!}>
                                        {{ $task->status?->status_name ?? 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @php
                                        $days = $task->aging_days ?? 0;
                                    @endphp
                                    <div class="flex flex-col items-end">
                                        <div class="font-black font-mono text-base {{ $days > 7 ? 'text-red-500' : 'text-amber-500' }}">
                                            {{ $days }} <span class="text-[10px] font-bold uppercase tracking-widest font-sans text-gray-400">Days</span>
                                        </div>
                                        @if($days > 7)
                                            <span class="text-[9px] bg-red-50 text-red-600 px-1.5 rounded mt-1 font-bold uppercase tracking-wider border border-red-100 no-print">Critical</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <div class="bg-gray-50 rounded-full h-16 w-16 flex items-center justify-center mx-auto mb-4 border border-gray-100 shadow-sm">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
                                    </div>
                                    <p class="text-gray-900 font-bold text-sm">No outstanding tasks found</p>
                                    <p class="text-gray-500 text-xs mt-1">The active queue is completely clear!</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- TASK DETAILS MODAL (Pop-up) --}}
        <div x-show="showModal" x-cloak class="relative z-[100] no-print" aria-labelledby="modal-title" role="dialog" aria-modal="true">
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

    {{-- CSV EXPORT JAVASCRIPT --}}
    <script>
        function exportToCSV(filename) {
            let csv = [];
            
            // Reusable function to scrape an HTML table and convert to CSV format
            function parseTable(tableId, title) {
                let table = document.getElementById(tableId);
                if (!table) return;
                
                let rows = table.querySelectorAll('tr');
                if(rows.length <= 1) return; // Skip if empty table
                
                csv.push('"' + title + '"'); 
                
                for (let i = 0; i < rows.length; i++) {
                    let row = [], cols = rows[i].querySelectorAll('td, th');
                    for (let j = 0; j < cols.length; j++) {
                        // Strip out new lines, extra spaces, and vertical bars used in the UI
                        let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/\|/g, '').replace(/\s+/g, ' ').trim();
                        // Escape quotes for CSV safety
                        row.push('"' + data.replace(/"/g, '""') + '"');
                    }
                    csv.push(row.join(','));
                }
                csv.push(''); // Empty line to separate the two tables
            }

            // Grab the data from both tables
            parseTable('productivity-table', 'PRODUCTIVITY LEDGER');
            parseTable('aging-table', 'STANDING DATA (AGING)');

            if (csv.length === 0) {
                alert('No data is currently available to export.');
                return;
            }

            // Use \uFEFF to ensure Excel opens the CSV correctly with UTF-8 encoding
            let csvFile = new Blob(["\uFEFF" + csv.join('\n')], {type: 'text/csv;charset=utf-8;'});
            let downloadLink = document.createElement('a');
            
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
</x-app-layout>