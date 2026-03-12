<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.min.js"></script>

    <style>
        /* BASE GRID & TYPOGRAPHY */
        .gantt .grid-header { fill: #f8fafc; stroke: #f1f5f9; }
        .gantt .grid-row { fill: #ffffff; stroke: #f8fafc; }
        .gantt .grid-row:hover { fill: #f8fafc; }
        .gantt .tick text { fill: #94a3b8; font-family: 'Figtree', sans-serif; font-size: 11px; font-weight: 700; }
        .gantt .bar-label { font-family: 'Figtree', sans-serif; font-weight: 800; font-size: 12px; fill: #ffffff; }

        /* THEMED PILL BARS */
        .project-header-row .bar { fill: #800000; cursor: pointer; transition: fill 0.2s; } 
        .project-header-row .bar:hover { fill: #600000; }
        
        .task-normal .bar { fill: #000080; cursor: pointer; transition: fill 0.2s; } 
        .task-normal .bar:hover { fill: #000066; }
        .task-normal .bar-progress { fill: rgba(255, 255, 255, 0.2); } /* Subtle progress overlay */
        
        .task-emergency .bar { fill: #ef4444; }
        .task-emergency .bar-progress { fill: rgba(255, 255, 255, 0.2); }
        
        .task-completed .bar { fill: #10b981; }

        /* ACTION BUTTONS (SVG Overlays) */
        .gantt-action-btn { cursor: pointer; outline: none; }
        .gantt-action-btn circle { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); stroke: #ffffff; stroke-width: 2; }
        .gantt-action-btn:hover circle { transform: scale(1.1); }
        .btn-project circle { fill: #4a0000; } /* Darker Maroon for button */
        .btn-task circle { fill: #ffffff; }   /* White button for tasks */
        .btn-task path { fill: #000080; }     /* Blue icon inside white button */

        /* PREMIUM KANBAN-STYLE POPUP */
        .gantt-container .popup-wrapper {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0,0,0,0.05);
            padding: 20px;
            min-width: 280px;
            font-family: 'Figtree', sans-serif;
            border: none;
            transform: translateY(-5px);
        }
        
        .pop-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .pop-badge { font-size: 9px; font-weight: 900; padding: 4px 10px; border-radius: 99px; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-emergency { background: #fee2e2; color: #991b1b; }
        .badge-normal { background: #f1f5f9; color: #000080; }
        
        .pop-id { font-size: 11px; font-weight: 800; color: #94a3b8; }
        .pop-title { font-weight: 900; color: #1e293b; font-size: 16px; line-height: 1.3; margin-bottom: 16px; }
        
        .pop-assignee { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; background: #f8fafc; padding: 8px 12px; border-radius: 12px; }
        .pop-avatar { width: 28px; height: 28px; border-radius: 50%; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 900; }
        .pop-name { font-size: 13px; color: #334155; font-weight: 700; }

        .pop-progress-container { margin-top: 10px; }
        .pop-progress-bar { width: 100%; height: 6px; background: #e2e8f0; border-radius: 99px; overflow: hidden; margin-bottom: 6px; }
        .pop-progress-fill { height: 100%; background: #000080; border-radius: 99px; }
        .pop-progress-text { display: flex; justify-content: space-between; font-size: 11px; font-weight: 800; color: #64748b; }
    </style>

    <div id="gantt-data-storage" class="hidden" data-tasks="{{ json_encode($ganttTasks) }}"></div>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-[100%] mx-auto">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            
            {{-- Header Toolbar --}}
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-white">
                <div>
                    <h2 class="font-black text-2xl uppercase tracking-tight text-gray-900">Timeline Matrix</h2>
                    <p class="text-xs font-bold text-gray-400 mt-1">Interactive Project Roadmap</p>
                </div>
                <div class="flex bg-gray-50 p-1 rounded-xl border border-gray-100">
                    <button onclick="toggleAll(true)" class="px-4 py-2 text-[10px] font-black text-blue-800 hover:bg-white hover:shadow-sm rounded-lg transition-all">EXPAND ALL</button>
                    <button onclick="toggleAll(false)" class="px-4 py-2 text-[10px] font-black text-maroon-800 hover:bg-white hover:shadow-sm rounded-lg transition-all" style="color: #800000;">COLLAPSE ALL</button>
                </div>
            </div>

            {{-- Gantt Container --}}
            <div class="overflow-x-auto min-h-[600px] p-4 bg-gray-50/30">
                <svg id="gantt"></svg>
            </div>
        </div>
    </div>

    <script>
        let openProjects = new Set();
        const rawTasks = JSON.parse(document.getElementById('gantt-data-storage').getAttribute('data-tasks') || '[]');

        function renderGantt() {
            const filteredTasks = rawTasks.filter(t => t.is_project || openProjects.has(t.project_id))
                .map(t => {
                    // Clean names for display
                    if (t.is_project) return { ...t, name: t.name.replace(/[▶▼]\s*/, '') };
                    return { ...t, name: t.name.replace('↳ ', '') };
                });

            new Gantt("#gantt", filteredTasks, {
                header_height: 60,
                column_width: 35,
                view_mode: 'Month',
                bar_height: 38,
                bar_corner_radius: 19, // Fully rounded pill shape
                custom_popup_html: function(task) {
                    if (task.is_project) {
                        return `
                            <div class="p-2">
                                <div class="text-[10px] font-black text-gray-400 mb-1">PROJECT DIR.</div>
                                <div class="text-sm font-black" style="color: #800000;">${task.name}</div>
                            </div>
                        `;
                    }

                    const initial = task.assignee_name ? task.assignee_name.charAt(0).toUpperCase() : '?';
                    const isEmergency = task.priority_name.toLowerCase() === 'emergency';
                    const badgeClass = isEmergency ? 'badge-emergency' : 'badge-normal';
                    
                    // Visual Progress Bar Color
                    let progressColor = '#000080';
                    if (task.status_name.toLowerCase().includes('completed')) progressColor = '#10b981';
                    if (isEmergency) progressColor = '#ef4444';

                    return `
                        <div>
                            <div class="pop-header">
                                <span class="pop-badge ${badgeClass}">${task.priority_name}</span>
                                <span class="pop-id">#${task.id.split('_')[1]}</span>
                            </div>
                            
                            <div class="pop-title">${task.name}</div>
                            
                            <div class="pop-assignee">
                                <div class="pop-avatar">${initial}</div>
                                <div class="pop-name">${task.assignee_name}</div>
                            </div>
                            
                            <div class="pop-progress-container">
                                <div class="pop-progress-bar">
                                    <div class="pop-progress-fill" style="width: ${task.progress}%; background-color: ${progressColor};"></div>
                                </div>
                                <div class="pop-progress-text">
                                    <span style="color: ${progressColor};">${task.status_name}</span>
                                    <span>${task.progress}%</span>
                                </div>
                            </div>
                        </div>
                    `;
                },
                on_click: function(task) {
                    if (task.is_project) toggleProject(task.project_id);
                    else window.location.href = task.url;
                }
            });

            injectCircularButtons();
        }

        function injectCircularButtons() {
            setTimeout(() => {
                const bars = document.querySelectorAll('.bar-wrapper');
                bars.forEach(group => {
                    const id = group.getAttribute('data-id');
                    const task = rawTasks.find(t => t.id === id);
                    if (!task) return;

                    const bar = group.querySelector('.bar');
                    const x = parseFloat(bar.getAttribute('x'));
                    const y = parseFloat(bar.getAttribute('y'));
                    const w = parseFloat(bar.getAttribute('width'));
                    const h = parseFloat(bar.getAttribute('height'));

                    // Create Action Button Group
                    const g = document.createElementNS("http://www.w3.org/2000/svg", "g");
                    g.setAttribute("class", task.is_project ? "gantt-action-btn btn-project" : "gantt-action-btn btn-task");
                    
                    // Overlap the button on the right edge of the pill
                    const radius = 14;
                    g.setAttribute("transform", `translate(${x + w - radius - 4}, ${y + (h/2)})`);
                    
                    const circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
                    circle.setAttribute("r", radius);

                    const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
                    path.setAttribute("stroke-width", "2.5");
                    path.setAttribute("stroke-linecap", "round");
                    path.setAttribute("stroke-linejoin", "round");
                    path.setAttribute("fill", "none");
                    
                    if (task.is_project) {
                        // Project Dropdown Icon (+ / -)
                        path.setAttribute("stroke", "#ffffff");
                        const isOpen = openProjects.has(task.project_id);
                        path.setAttribute("d", isOpen ? "M-4 0 L4 0" : "M-4 0 L4 0 M0 -4 L0 4");
                        g.onclick = (e) => { e.stopPropagation(); toggleProject(task.project_id); };
                    } else {
                        // Task Navigation Arrow (->)
                        path.setAttribute("stroke", "#000080");
                        if (task.priority_name.toLowerCase() === 'emergency') path.setAttribute("stroke", "#ef4444");
                        
                        path.setAttribute("d", "M-3 -4 L2 0 L-3 4"); 
                        path.setAttribute("transform", "translate(1, 0)"); // visual center adjustment
                        g.onclick = (e) => { e.stopPropagation(); window.location.href = task.url; };
                    }

                    g.appendChild(circle);
                    g.appendChild(path);
                    group.appendChild(g);
                });
            }, 50);
        }

        function toggleProject(pid) {
            if (openProjects.has(pid)) openProjects.delete(pid);
            else openProjects.add(pid);
            renderGantt();
        }

        function toggleAll(expand) {
            if (expand) rawTasks.forEach(t => t.is_project && openProjects.add(t.project_id));
            else openProjects.clear();
            renderGantt();
        }

        if (rawTasks.length > 0) renderGantt();
    </script>
</x-app-layout>