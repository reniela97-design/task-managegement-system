<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
        .fc-toolbar-title { font-size: 1.25rem !important; color: #1e3a8a; font-weight: 800; text-transform: uppercase; }
        .fc-button-primary { background-color: #1e3a8a !important; border-color: #1e3a8a !important; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; }
        .fc-day-today { background-color: #eff6ff !important; }
        .fc-event { border: none; border-radius: 4px; padding: 3px 6px; font-size: 0.7rem; font-weight: 600; cursor: pointer; transition: transform 0.1s; }
        .fc-event:hover { transform: scale(1.02); z-index: 50; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
    </style>

    {{-- HIDDEN DATA STORAGE --}}
    <div id="app-data" class="hidden"
         data-events="{{ json_encode($events) }}"
         data-save-route="{{ route('reports.saveNote') }}"
         data-csrf="{{ csrf_token() }}">
    </div>

    <div id="calendar-wrapper" x-data="calendarData()">
        {{-- Header Section --}}
        <div class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <div>
                    <h2 class="font-black text-2xl text-blue-900 uppercase tracking-tight">System Calendar</h2>
                    <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">Active Operations & Notes</p>
                </div>
                <a href="{{ route('reports.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2 px-5 rounded-xl shadow-sm transition uppercase tracking-wide text-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Performance Reports
                </a>
            </div>
        </div>

        <div class="py-8 max-w-[98%] mx-auto sm:px-6 lg:px-8">
            
            {{-- Admin Filter --}}
            @if(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager'))
            <div class="bg-white border border-gray-200 rounded-2xl p-4 flex flex-col md:flex-row items-center justify-between shadow-sm mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 text-blue-700 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg></div>
                    <span class="text-blue-900 font-black uppercase text-xs tracking-widest">Filter Calendar By Personnel</span>
                </div>
                <form method="GET" action="{{ route('reports.calendar') }}" class="w-full md:w-64 mt-3 md:mt-0">
                    <select name="user_id" class="w-full text-sm rounded-xl border-gray-300 focus:border-blue-900 bg-gray-50 shadow-sm" onchange="this.form.submit()">
                        <option value="">-- All Personnel --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->user_id }}" {{ request('user_id') == $u->user_id ? 'selected' : '' }}>{{ $u->user_name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-6">
                
                {{-- LEFT SIDEBAR: MY TASKS --}}
                <div class="w-full lg:w-1/4 flex flex-col gap-6">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 flex flex-col h-[750px] overflow-hidden">
                        <div class="p-5 bg-gradient-to-r from-blue-900 to-indigo-900 border-b border-indigo-800">
                            <h3 class="font-black text-white uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                My Tasks Hub
                            </h3>
                        </div>
                        
                        <div class="p-4 overflow-y-auto flex-1 custom-scrollbar space-y-6">
                            
                            {{-- In Progress List --}}
                            <div>
                                <h4 class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-3 flex justify-between border-b border-indigo-100 pb-1">
                                    Working On <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded">{{ $sidebarTasks['working']->count() ?? 0 }}</span>
                                </h4>
                                <div class="space-y-2">
                                    @forelse($sidebarTasks['working'] ?? [] as $st)
                                        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 hover:shadow-md transition">
                                            <a href="{{ route('tasks.show', $st->task_id) }}" class="font-bold text-xs text-indigo-900 block leading-tight hover:underline">{{ $st->task_title }}</a>
                                            <div class="mt-2 flex justify-between items-center text-[9px] font-bold uppercase">
                                                <span class="text-indigo-500">{{ $st->project->project_name ?? 'Gen' }}</span>
                                                <span class="text-red-500">{{ $st->task_due_date ? \Carbon\Carbon::parse($st->task_due_date)->format('M d') : 'No Due' }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-xs text-gray-400 italic">No tasks currently in progress.</p>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Pending List --}}
                            <div>
                                <h4 class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-3 flex justify-between border-b border-blue-100 pb-1">
                                    Pending (To Do) <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded">{{ $sidebarTasks['pending']->count() ?? 0 }}</span>
                                </h4>
                                <div class="space-y-2">
                                    @forelse($sidebarTasks['pending'] ?? [] as $st)
                                        <div class="bg-white border border-gray-200 rounded-lg p-3 hover:shadow-md transition">
                                            <a href="{{ route('tasks.show', $st->task_id) }}" class="font-bold text-xs text-gray-800 block leading-tight hover:text-blue-600 transition">{{ $st->task_title }}</a>
                                            <div class="mt-2 flex justify-between items-center text-[9px] font-bold uppercase">
                                                <span class="text-gray-400">{{ $st->project->project_name ?? 'Gen' }}</span>
                                                <span class="text-red-500">{{ $st->task_due_date ? \Carbon\Carbon::parse($st->task_due_date)->format('M d') : 'No Due' }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-xs text-gray-400 italic">No pending tasks found.</p>
                                    @endforelse
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- RIGHT MAIN: CALENDAR --}}
                <div class="w-full lg:w-3/4">
                    <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-100">
                        {{-- Legend --}}
                        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex flex-wrap justify-between items-center gap-4">
                            <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-slate-600">
                                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-800"></span> Emergency</div>
                                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-900"></span> Normal</div>
                                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-slate-500"></span> Low Priority</div>
                                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-600"></span> Done</div>
                            </div>
                            <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-amber-700 bg-yellow-100 px-3 py-1 rounded-lg">
                                <span class="w-3 h-3 rounded-full bg-yellow-400"></span> Private Notes (Click Date to Add)
                            </div>
                        </div>

                        <div class="p-6 relative z-0">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- MODAL 1: TASK INFO POPUP --}}
        <div x-show="showTaskModal" x-cloak class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showTaskModal" x-transition.opacity class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm" @click="showTaskModal = false"></div>
            <div class="fixed inset-0 z-[101] w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div x-show="showTaskModal" x-transition.scale.95 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-md">
                        <div class="px-6 py-6" :class="taskData.is_emergency ? 'bg-red-900' : 'bg-blue-900'">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 pr-4">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[9px] font-black uppercase tracking-widest bg-white/20 text-white mb-2" x-text="taskData.system"></span>
                                    <h3 class="text-xl font-black text-white leading-tight" x-text="taskData.title"></h3>
                                </div>
                                <button @click="showTaskModal = false" class="text-white/60 hover:text-white transition p-1"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                            </div>
                        </div>
                        <div class="bg-white p-6 space-y-6">
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-black text-sm shadow-md" x-text="String(taskData.assigned_to).charAt(0)"></div>
                                <div>
                                    <h4 class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Personnel Assigned</h4>
                                    <p class="text-sm font-black text-indigo-900" x-text="taskData.assigned_to"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-y-6 gap-x-4 border-t border-slate-100 pt-6">
                                <div><h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Client</h4><p class="text-xs font-bold text-gray-800" x-text="taskData.client"></p></div>
                                <div><h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Project</h4><p class="text-xs font-bold text-gray-800" x-text="taskData.project"></p></div>
                                <div><h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Target Due</h4><p class="text-xs font-bold text-red-600" x-text="taskData.due"></p></div>
                                <div x-show="taskData.is_completed"><h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Finished Date</h4><p class="text-xs font-black text-emerald-600" x-text="taskData.finished_at"></p></div>
                                <div><h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</h4><span class="inline-block mt-1 px-2 py-0.5 text-[9px] font-black uppercase rounded bg-gray-100 text-gray-600 border border-gray-200" x-text="taskData.status"></span></div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex gap-3 border-t border-gray-100">
                            <a :href="taskData.url" class="w-full inline-flex justify-center items-center rounded-xl px-4 py-3 text-xs font-black text-white shadow-lg transition-all" :class="taskData.is_emergency ? 'bg-red-900 hover:bg-red-800' : 'bg-blue-900 hover:bg-blue-800'">
                                OPEN WORKSPACE →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL 2: PERSONAL NOTE POPUP --}}
        <div x-show="showNoteModal" x-cloak class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="showNoteModal" x-transition.opacity class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm" @click="showNoteModal = false"></div>
            <div class="fixed inset-0 z-[101] w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div x-show="showNoteModal" x-transition.scale.95 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-md border-t-8 border-yellow-400">
                        <div class="px-6 py-5 flex justify-between items-center bg-yellow-50 border-b border-yellow-100">
                            <h3 class="text-lg font-black text-yellow-900 uppercase flex items-center gap-2">
                                📝 Private Note
                            </h3>
                            <button @click="showNoteModal = false" class="text-yellow-600 hover:text-yellow-900"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </div>
                        <div class="bg-white p-6">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Note for: <span class="text-indigo-600" x-text="noteData.note_date"></span></p>
                            <textarea x-model="noteData.note_text" rows="5" class="w-full rounded-xl border-gray-300 bg-yellow-50/50 shadow-inner focus:border-yellow-400 focus:ring-yellow-400 text-sm" placeholder="Type your private reminder here... (Admins cannot see this)"></textarea>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t border-gray-100">
                            <p class="text-[9px] font-bold text-gray-400 uppercase">Clear text to delete</p>
                            <button @click="saveNote" type="button" class="inline-flex justify-center items-center rounded-xl bg-yellow-400 hover:bg-yellow-500 px-6 py-2.5 text-xs font-black text-yellow-900 shadow-md transition-all">
                                Save Note
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- PURE JAVASCRIPT --}}
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>
        function calendarData() {
            return {
                showTaskModal: false,
                taskData: {},
                showNoteModal: false,
                noteData: { note_date: '', note_text: '' },
                
                openNote(dateStr, existingText = '') {
                    this.noteData.note_date = dateStr;
                    this.noteData.note_text = existingText;
                    this.showNoteModal = true;
                },

                async saveNote() {
                    try {
                        const appData = document.getElementById('app-data');
                        const saveRoute = appData.getAttribute('data-save-route');
                        const csrf = appData.getAttribute('data-csrf');

                        let res = await fetch(saveRoute, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf
                            },
                            body: JSON.stringify(this.noteData)
                        });
                        
                        if(res.ok) {
                            window.location.reload(); 
                        } else {
                            alert('Error saving note.');
                        }
                    } catch(e) {
                        console.error(e);
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            if (calendarEl) {
                const rawEvents = document.getElementById('app-data').getAttribute('data-events');
                const parsedEvents = JSON.parse(rawEvents);

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listWeek' },
                    height: 'auto',
                    dayMaxEvents: 2,
                    events: parsedEvents,
                    eventOrder: 'priority_sort',
                    
                    eventClick: function(info) {
                        info.jsEvent.preventDefault();
                        let alpine = Alpine.$data(document.getElementById('calendar-wrapper'));
                        let p = info.event.extendedProps;

                        if (p.type === 'note') {
                            alpine.openNote(p.note_date, p.note_text);
                        } else {
                            alpine.taskData = { 
                                title: p.full_title, system: p.system, client: p.client, 
                                project: p.project, status: p.status, due: p.due_date, 
                                url: info.event.url, is_completed: p.is_completed, 
                                finished_at: p.finished_at, is_emergency: p.is_emergency, 
                                assigned_to: p.assigned_to 
                            };
                            alpine.showTaskModal = true;
                        }
                    },

                    dateClick: function(info) {
                        let alpine = Alpine.$data(document.getElementById('calendar-wrapper'));
                        let existingNote = calendar.getEvents().find(e => e.extendedProps.type === 'note' && e.startStr === info.dateStr);
                        
                        if (existingNote) {
                            alpine.openNote(info.dateStr, existingNote.extendedProps.note_text);
                        } else {
                            alpine.openNote(info.dateStr, '');
                        }
                    }
                });
                calendar.render();
            }
        });
    </script>
</x-app-layout>