<x-app-layout>
    {{-- Frappe Gantt Library --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.min.js"></script>

    <style>
        /* REDESIGNED FRAPPE GANTT CSS */
        .gantt .grid-header { fill: #f8fafc; stroke: #e2e8f0; }
        .gantt .grid-row { fill: #ffffff; stroke: #f1f5f9; transition: fill 0.3s ease; }
        .gantt .grid-row:nth-child(even) { fill: #fcfcfc; }
        .gantt .grid-row:hover { fill: #f1f5f9; } /* Row hover effect */
        .gantt .tick text { fill: #64748b; font-family: 'Figtree', sans-serif; font-size: 11px; font-weight: 700; }
        .gantt .tick line { stroke: #e2e8f0; }
        
        /* Premium Bar Colors with slight transparency for a modern look */
        .gantt .bar-wrapper { cursor: pointer; }
        .gantt .bar-wrapper:hover .bar { opacity: 0.8; }
        
        /* Normal Tasks - Blue */
        .task-normal .bar { fill: #3b82f6; }
        .task-normal .bar-progress { fill: #1e40af; }
        
        /* Emergency Tasks - Red */
        .task-emergency .bar { fill: #ef4444; }
        .task-emergency .bar-progress { fill: #991b1b; }
        
        /* Completed Tasks - Green */
        .task-completed .bar { fill: #10b981; }
        .task-completed .bar-progress { fill: #065f46; }

        .gantt .bar-label { fill: #ffffff; font-family: 'Figtree', sans-serif; font-weight: 800; font-size: 11px; letter-spacing: 0.5px; }
        
        /* Redesigned Tooltip */
        .gantt-container .popup-wrapper { padding: 0; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
        .gantt-container .popup-wrapper .title { background: #0f172a; border-radius: 0; font-family: 'Figtree', sans-serif; font-weight: 800; padding: 12px 16px; color: white; border-bottom: 2px solid #3b82f6; }
        .gantt-container .popup-wrapper .subtitle { font-family: 'Figtree', sans-serif; font-size: 12px; font-weight: 600; color: #475569; padding: 12px 16px; background: white; }

        /* Dark Mode Overrides */
        .dark .gantt .grid-header { fill: #0f172a; stroke: #334155; }
        .dark .gantt .grid-row { fill: #1e293b; stroke: #334155; }
        .dark .gantt .grid-row:nth-child(even) { fill: #0f172a; }
        .dark .gantt .grid-row:hover { fill: #334155; }
        .dark .gantt .tick text { fill: #94a3b8; }
        .dark .gantt .tick line { stroke: #334155; }
        .dark .gantt-container .popup-wrapper { border: 1px solid #334155; }
        .dark .gantt-container .popup-wrapper .subtitle { background: #1e293b; color: #cbd5e1; }
    </style>

    {{-- HIDDEN DATA STORAGE --}}
    <div id="gantt-data-storage" class="hidden" data-tasks="{{ json_encode($ganttTasks) }}"></div>

    {{-- NEW MODERN HEADER --}}
    <div class="bg-white border-b border-gray-200 shadow-sm relative z-50 dark:bg-slate-900 dark:border-slate-800">
        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-50 dark:bg-indigo-900/50 rounded-xl border border-indigo-100 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl text-gray-900 dark:text-white uppercase tracking-tight">
                        Timeline Matrix
                    </h2>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-0.5">Interactive Project Roadmap</p>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('gantt.index') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                
                {{-- Year Filter --}}
                <div class="w-full sm:w-auto relative">
                    <select name="filter_year" onchange="this.form.submit()" class="w-full sm:w-40 text-sm font-bold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 py-2.5 dark:bg-slate-800 dark:border-slate-700 dark:text-white transition">
                        <option value="">-- All Years --</option>
                        @foreach(range(2023, now()->addYear()->year) as $y)
                            <option value="{{ $y }}" {{ request('filter_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Project Filter --}}
                <div class="w-full sm:w-auto relative">
                    <select name="project_id" onchange="this.form.submit()" class="w-full sm:w-56 text-sm font-bold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 py-2.5 dark:bg-slate-800 dark:border-slate-700 dark:text-white transition">
                        <option value="">-- All Projects --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->project_id }}" {{ request('project_id') == $project->project_id ? 'selected' : '' }}>
                                {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
            </form>
        </div>
    </div>

    <div class="py-8">
        <div class="max-w-[98%] mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white shadow-lg shadow-indigo-100/20 rounded-2xl border border-gray-200 overflow-hidden dark:bg-slate-900 dark:border-slate-800 dark:shadow-none relative">
                
                {{-- Top Color Bar Accent --}}
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

                {{-- Toolbar --}}
                <div class="p-5 border-b border-gray-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
                    
                    {{-- Legend --}}
                    <div class="flex flex-wrap items-center gap-4 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-slate-800 py-2 px-4 rounded-xl border border-gray-200 dark:border-slate-700">
                        <span class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-blue-500 shadow-sm"></span> Normal</span>
                        <span class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-red-500 shadow-sm"></span> Emergency</span>
                        <span class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-emerald-500 shadow-sm"></span> Completed</span>
                    </div>
                    
                    {{-- Zoom Controls (Redesigned) --}}
                    <div class="flex bg-gray-100 border border-gray-200 dark:bg-slate-800 dark:border-slate-700 rounded-xl p-1 shadow-inner" id="zoom-controls">
                        <button data-zoom="Quarter Day" class="px-4 py-1.5 text-xs font-bold text-gray-500 hover:text-indigo-600 rounded-lg transition dark:text-gray-400 dark:hover:text-white">Day</button>
                        <button data-zoom="Half Day" class="px-4 py-1.5 text-xs font-bold text-gray-500 hover:text-indigo-600 rounded-lg transition dark:text-gray-400 dark:hover:text-white">Week</button>
                        <button data-zoom="Month" class="px-4 py-1.5 text-xs font-black text-indigo-700 bg-white shadow-sm rounded-lg transition dark:bg-slate-700 dark:text-indigo-400 border border-gray-200 dark:border-slate-600">Month</button>
                    </div>
                </div>

                {{-- The Gantt Chart Container --}}
                <div class="overflow-x-auto p-2 custom-scrollbar bg-gray-50/30 dark:bg-slate-900/50 min-h-[500px]">
                    @if(count($ganttTasks) > 0)
                        <svg id="gantt"></svg>
                    @else
                        <div class="py-32 text-center flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                            <div class="p-6 bg-gray-50 dark:bg-slate-800 rounded-full mb-4 border border-gray-100 dark:border-slate-700 shadow-inner">
                                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="font-black text-gray-800 text-xl tracking-tight dark:text-white">No Timeline Data Found</p>
                            <p class="text-sm mt-2 font-medium">Try adjusting your filters or adding start/due dates to your tasks.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- INITIALIZATION SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const storage = document.getElementById('gantt-data-storage');
            const tasks = JSON.parse(storage.getAttribute('data-tasks') || '[]');

            if(tasks.length > 0) {
                var gantt = new Gantt("#gantt", tasks, {
                    header_height: 55,
                    column_width: 35,
                    step: 24,
                    readonly: true,
                    view_modes: ['Quarter Day', 'Half Day', 'Day', 'Week', 'Month'],
                    bar_height: 32,
                    bar_corner_radius: 8, // Softer curves for modern look
                    arrow_curve: 5,
                    padding: 20,
                    view_mode: 'Month',
                    date_format: 'YYYY-MM-DD',
                    
                    on_click: function (task) {
                        if(task.url) { window.location.href = task.url; }
                    }
                });

                // Zoom Control Logic
                document.querySelectorAll('#zoom-controls button').forEach(button => {
                    button.addEventListener('click', e => {
                        // Reset all buttons to inactive state
                        document.querySelectorAll('#zoom-controls button').forEach(b => {
                            b.classList.remove('bg-white', 'text-indigo-700', 'shadow-sm', 'dark:bg-slate-700', 'dark:text-indigo-400', 'font-black', 'border', 'border-gray-200', 'dark:border-slate-600');
                            b.classList.add('text-gray-500', 'font-bold', 'dark:text-gray-400');
                        });
                        
                        // Apply active state to clicked button
                        e.target.classList.add('bg-white', 'text-indigo-700', 'shadow-sm', 'dark:bg-slate-700', 'dark:text-indigo-400', 'font-black', 'border', 'border-gray-200', 'dark:border-slate-600');
                        e.target.classList.remove('text-gray-500', 'font-bold', 'dark:text-gray-400');
                        
                        // Update Gantt view
                        gantt.change_view_mode(e.target.getAttribute('data-zoom'));
                    });
                });
            }
        });
    </script>
</x-app-layout>