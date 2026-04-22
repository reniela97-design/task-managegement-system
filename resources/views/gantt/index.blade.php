<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.min.js"></script>

    <style>
        /* BASE GRID & TYPOGRAPHY */
        .gantt .grid-header { fill: #ffffff; stroke: #e2e8f0; }
        .gantt .grid-row { fill: #ffffff; stroke: #f1f5f9; }
        .gantt .grid-row:nth-child(even) { fill: #f8fafc; }
        .gantt .tick text { fill: #64748b; font-family: 'Figtree', sans-serif; font-size: 10px; }
        
        /* BAR STYLING */
        .gantt .bar-label { font-family: 'Figtree', sans-serif; font-weight: 600; font-size: 12px; fill: #334155; }
        .gantt .bar-wrapper { cursor: pointer; }
        
        /* Theme Colors */
        .theme-blue.project-summary-bar .bar { fill: #60a5fa; fill-opacity: 0.5; stroke: #3b82f6; stroke-width: 1; }
        .theme-blue.task-bar .bar { fill: #eff6ff; stroke: #3b82f6; stroke-width: 1; }
        .theme-blue .bar-progress { fill: #3b82f6; }

        .theme-green.project-summary-bar .bar { fill: #4ade80; fill-opacity: 0.5; stroke: #22c55e; stroke-width: 1; }
        .theme-green.task-bar .bar { fill: #f0fdf4; stroke: #22c55e; stroke-width: 1; }
        .theme-green .bar-progress { fill: #22c55e; }

        .theme-purple.project-summary-bar .bar { fill: #c084fc; fill-opacity: 0.5; stroke: #a855f7; stroke-width: 1; }
        .theme-purple.task-bar .bar { fill: #faf5ff; stroke: #a855f7; stroke-width: 1; }
        .theme-purple .bar-progress { fill: #a855f7; }

        .theme-orange.project-summary-bar .bar { fill: #fb923c; fill-opacity: 0.5; stroke: #f97316; stroke-width: 1; }
        .theme-orange.task-bar .bar { fill: #fff7ed; stroke: #f97316; stroke-width: 1; }
        .theme-orange .bar-progress { fill: #f97316; }

        .theme-teal.project-summary-bar .bar { fill: #2dd4bf; fill-opacity: 0.5; stroke: #14b8a6; stroke-width: 1; }
        .theme-teal.task-bar .bar { fill: #f0fdfa; stroke: #14b8a6; stroke-width: 1; }
        .theme-teal .bar-progress { fill: #14b8a6; }

        /* Label overrides */
        .task-bar .bar-label { fill: #1e293b; font-weight: 500; }
        .project-summary-bar .bar-label { fill: #0f172a; font-weight: 800; }

        /* COMPLETED TASKS STYLING (Locked) */
        .completed-task .bar { fill-opacity: 0.6; stroke-dasharray: 4, 4; }
        .completed-task .bar-wrapper { cursor: not-allowed !important; }
        .completed-task .bar-label { fill: #64748b; font-style: italic; }
        .completed-task .handle-group { display: none !important; } /* Removes drag handles */
        
        /* Popup base overrides to make Tailwind play nice with Frappe Gantt */
        .gantt .popup-wrapper { padding: 0 !important; }
    </style>

    <div id="gantt-data-storage" class="hidden" data-tasks="{{ json_encode($ganttTasks) }}"></div>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-[100%] mx-auto">
        
        {{-- Filters & Controls Area --}}
        <div class="bg-white rounded-t-xl border border-gray-200 p-4 flex flex-col xl:flex-row justify-between items-end gap-4 shadow-sm relative z-10">
            
            {{-- Filter Form --}}
            <form method="GET" action="{{ route('gantt.index') }}" class="flex flex-wrap items-end gap-3 w-full xl:w-auto">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Project</label>
                    <select name="project_id" class="border-gray-300 rounded-md text-sm py-1.5 focus:ring-blue-500 focus:border-blue-500 min-w-[150px]">
                        <option value="">All Projects</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->project_id }}" {{ request('project_id') == $p->project_id ? 'selected' : '' }}>
                                {{ $p->project_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Month</label>
                    <select name="filter_month" class="border-gray-300 rounded-md text-sm py-1.5 focus:ring-blue-500 focus:border-blue-500 min-w-[120px]">
                        <option value="">All</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('filter_month') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Year</label>
                    <select name="filter_year" class="border-gray-300 rounded-md text-sm py-1.5 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        @for($y = date('Y') - 1; $y <= date('Y') + 3; $y++)
                            <option value="{{ $y }}" {{ request('filter_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-md text-sm font-semibold transition-colors">Apply Filter</button>
                    @if(request()->has('project_id') || request()->has('filter_year') || request()->has('filter_month'))
                        <a href="{{ route('gantt.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-1.5 rounded-md text-sm font-semibold transition-colors">Clear</a>
                    @endif
                </div>
            </form>

            {{-- Right Side Controls --}}
            <div class="flex flex-col sm:flex-row items-end sm:items-center gap-4">
                {{-- Expand / Collapse All Buttons --}}
                <div class="flex gap-4 px-1">
                    <button type="button" onclick="toggleAll(true)" class="text-xs font-bold text-gray-500 hover:text-blue-600 transition-colors flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg> Expand All
                    </button>
                    <button type="button" onclick="toggleAll(false)" class="text-xs font-bold text-gray-500 hover:text-blue-600 transition-colors flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"></path></svg> Collapse All
                    </button>
                </div>

                {{-- View Mode Toggles --}}
                <div class="flex bg-gray-100 p-1 rounded-lg border border-gray-200">
                    <button onclick="changeViewMode('Quarter Day')" class="view-btn px-3 py-1 text-xs font-bold rounded-md text-gray-500 hover:text-gray-900" data-mode="Quarter Day">Hours</button>
                    <button onclick="changeViewMode('Day')" class="view-btn px-3 py-1 text-xs font-bold rounded-md bg-white text-blue-600 shadow-sm" data-mode="Day">Days</button>
                    <button onclick="changeViewMode('Week')" class="view-btn px-3 py-1 text-xs font-bold rounded-md text-gray-500 hover:text-gray-900" data-mode="Week">Weeks</button>
                    <button onclick="changeViewMode('Month')" class="view-btn px-3 py-1 text-xs font-bold rounded-md text-gray-500 hover:text-gray-900" data-mode="Month">Months</button>
                </div>
            </div>
        </div>

        {{-- Gantt Chart Container --}}
        <div class="bg-white rounded-b-xl shadow-sm border border-t-0 border-gray-200 overflow-hidden">
            <div class="overflow-x-auto min-h-[500px]">
                <svg id="gantt"></svg>
            </div>
        </div>
    </div>

    <script>
        let ganttChart;
        let currentViewMode = 'Day';
        
        const rawTasks = JSON.parse(document.getElementById('gantt-data-storage').getAttribute('data-tasks') || '[]');
        let openProjects = new Set(rawTasks.filter(t => t.is_project).map(t => t.project_id));

        function renderGantt() {
            const filteredTasks = rawTasks.filter(t => t.is_project || openProjects.has(t.project_id))
                .map(t => {
                    if (t.is_project) {
                        const icon = openProjects.has(t.project_id) ? '▼' : '▶';
                        return { ...t, name: `${icon} ${t.name.replace(/[▶▼]\s*/, '')}` };
                    }
                    return t;
                });

            const ganttContainer = document.getElementById('gantt');

            if (filteredTasks.length === 0) {
                ganttContainer.innerHTML = '<text x="20" y="40" font-family="sans-serif" font-size="14" fill="#64748b">No tasks match your filters.</text>';
                return;
            }

            ganttContainer.innerHTML = ''; 

            ganttChart = new Gantt("#gantt", filteredTasks, {
                header_height: 50,
                column_width: currentViewMode === 'Day' ? 30 : (currentViewMode === 'Month' ? 120 : 60),
                step: 24,
                view_modes: ['Quarter Day', 'Half Day', 'Day', 'Week', 'Month'],
                bar_height: 24,
                bar_corner_radius: 3,
                arrow_curve: 0,
                padding: 18,
                view_mode: currentViewMode,
                date_format: 'YYYY-MM-DD',
                
                // Tailwind Styled Popups
                custom_popup_html: function(task) {
                    const cleanName = task.name.replace(/[▶▼🔒]\s*/g, '');
                    
                    if (task.is_project) {
                        return `
                            <div class="bg-white p-4 rounded-lg shadow-xl border border-slate-200 min-w-[200px] z-50">
                                <div class="font-bold text-slate-800 text-sm mb-1">${cleanName}</div>
                                <div class="text-xs text-slate-500 mb-2">${task.start} — ${task.end}</div>
                                <div class="text-xs font-bold text-blue-600">Total Progress: ${task.progress}%</div>
                            </div>
                        `;
                    } else {
                        return `
                            <div class="bg-white p-4 rounded-lg shadow-xl border border-slate-200 min-w-[250px] z-50">
                                <div class="font-bold text-slate-800 text-sm mb-2 pb-2 border-b border-slate-100">${cleanName}</div>
                                <div class="grid grid-cols-2 gap-y-2 text-xs">
                                    <div class="text-slate-500 font-semibold">Start:</div>
                                    <div class="font-medium text-slate-800 text-right">${task.start}</div>
                                    
                                    <div class="text-slate-500 font-semibold">End:</div>
                                    <div class="font-medium text-slate-800 text-right">${task.end}</div>
                                    
                                    <div class="text-slate-500 font-semibold">Status:</div>
                                    <div class="font-medium text-slate-800 text-right">${task.status_name}</div>
                                    
                                    <div class="text-slate-500 font-semibold">Priority:</div>
                                    <div class="font-medium text-slate-800 text-right">${task.priority_name}</div>
                                    
                                    <div class="text-slate-500 font-semibold">Assignee:</div>
                                    <div class="font-medium text-slate-800 text-right">${task.assignee_name}</div>
                                </div>
                            </div>
                        `;
                    }
                },
                
                on_click: function(task) {
                    if (task.is_project) {
                        toggleProject(task.project_id);
                    } else {
                        if (task.is_completed) {
                            alert("This task is already completed and cannot be edited.");
                            return; 
                        }
                        window.location.href = task.url; 
                    }
                }
            });
            
            // Adjust label positions after render
            setTimeout(() => {
                document.querySelectorAll('.bar-label').forEach(label => {
                    label.setAttribute('x', parseFloat(label.getAttribute('x')) + 10);
                });
            }, 50);
        }

        function toggleProject(pid) {
            if (openProjects.has(pid)) {
                openProjects.delete(pid);
            } else {
                openProjects.add(pid);
            }
            renderGantt();
        }

        function toggleAll(expand) {
            if (expand) {
                rawTasks.forEach(t => { if (t.is_project) openProjects.add(t.project_id); });
            } else {
                openProjects.clear();
            }
            renderGantt(); 
        }

        function changeViewMode(mode) {
            currentViewMode = mode;
            document.querySelectorAll('.view-btn').forEach(btn => {
                if(btn.dataset.mode === mode) {
                    btn.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                    btn.classList.remove('text-gray-500');
                } else {
                    btn.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                    btn.classList.add('text-gray-500');
                }
            });
            if (ganttChart) renderGantt(); 
        }

        if (rawTasks.length > 0) renderGantt();
    </script>
</x-app-layout>