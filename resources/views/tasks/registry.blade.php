<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbars */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 8px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }

        /* TinyMCE rendering */
        .tinymce-content ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin-bottom: 0.5rem !important; }
        .tinymce-content ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin-bottom: 0.5rem !important; }
        .tinymce-content p { margin-bottom: 0.5rem !important; }
        .tinymce-content strong, .tinymce-content b { font-weight: bold !important; }
        .tinymce-content em, .tinymce-content i { font-style: italic !important; }
        .tinymce-content a { color: #3b82f6 !important; text-decoration: underline !important; }
    </style>

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 shadow-sm relative z-10">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-100 text-indigo-700 rounded-lg shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl text-indigo-900 uppercase tracking-tight">
                        {{ __('Task Registry') }}
                    </h2>
                    <p class="text-sm font-medium text-gray-500">Master database of all system assignments and records.</p>
                </div>
            </div>
            
            <a href="{{ route('tasks.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2.5 px-5 rounded-xl shadow-sm transition uppercase tracking-wide text-xs flex items-center gap-2 w-max">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                Switch to Kanban
            </a>
        </div>
    </div>

    {{-- WRAP MAIN CONTENT IN ALPINE DATA --}}
    <div class="py-10" x-data="{ showModal: false, modalData: {} }">
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

            {{-- Advanced Filters (Admin & Manager Only) --}}
            @if($isAdminOrManager)
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 mb-8">
                <form method="GET" action="{{ route('tasks.registry') }}" class="flex flex-col md:flex-row items-end gap-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 w-full">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Assignee</label>
                            <select name="assignee_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">All Personnel</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->user_id }}" {{ request('assignee_id') == $u->user_id ? 'selected' : '' }}>{{ $u->user_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Client</label>
                            <select name="client_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">All Clients</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->client_id }}" {{ request('client_id') == $c->client_id ? 'selected' : '' }}>{{ $c->client_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Project</label>
                            <select name="project_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">All Projects</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->project_id }}" {{ request('project_id') == $p->project_id ? 'selected' : '' }}>{{ $p->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Status</label>
                            <select name="status_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $s)
                                    <option value="{{ $s->status_id }}" {{ request('status_id') == $s->status_id ? 'selected' : '' }}>{{ $s->status_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('tasks.registry') }}" class="px-5 py-2.5 bg-gray-100 text-gray-500 hover:bg-gray-200 rounded-xl text-xs font-bold uppercase tracking-widest transition">Reset</a>
                        <button type="submit" class="px-8 py-2.5 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl text-xs font-bold uppercase tracking-widest shadow-md transition">Filter</button>
                    </div>
                </form>
            </div>
            @endif

            {{-- EMPTY STATE (If absolutely no tasks match the filter) --}}
            @if(!$hasAnyTasks)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 px-6 py-16 text-center text-gray-400 font-medium">
                    <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <p class="text-lg">No tasks found matching your criteria.</p>
                </div>
            @else

                {{-- 1. IN PROGRESS SECTION --}}
                @if($inProgressTasks->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-indigo-200 mb-8 relative">
                    <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                    <div class="bg-indigo-50/50 px-6 py-4 border-b border-indigo-100 flex items-center justify-between">
                        <h3 class="text-indigo-900 font-black uppercase text-sm tracking-widest flex items-center gap-2">
                            <span class="relative flex h-2.5 w-2.5">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-indigo-500"></span>
                            </span>
                            Active / In Progress
                        </h3>
                        <span class="bg-indigo-200 text-indigo-800 text-[10px] font-bold px-2.5 py-1 rounded-full">{{ $inProgressTasks->total() }} Tasks</span>
                    </div>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-indigo-50 text-[10px] uppercase tracking-widest text-indigo-400 font-bold">
                                    <th class="px-6 py-4 pl-8">Task Title</th>
                                    <th class="px-6 py-4">Due Date</th>
                                    <th class="px-6 py-4">Assignment</th>
                                    <th class="px-6 py-4">Priority</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-indigo-50">
                                @foreach($inProgressTasks as $task)
                                    @include('tasks.partials.registry-row', ['task' => $task, 'hoverColor' => 'indigo'])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($inProgressTasks->hasPages())
                        <div class="px-6 py-3 bg-indigo-50/30 border-t border-indigo-100">
                            {{ $inProgressTasks->links() }}
                        </div>
                    @endif
                </div>
                @endif

                {{-- 2. PENDING SECTION --}}
                @if($pendingTasks->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-blue-200 mb-8 relative">
                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                    <div class="bg-blue-50/50 px-6 py-4 border-b border-blue-100 flex items-center justify-between">
                        <h3 class="text-blue-900 font-black uppercase text-sm tracking-widest flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            Pending / To Do
                        </h3>
                        <span class="bg-blue-200 text-blue-800 text-[10px] font-bold px-2.5 py-1 rounded-full">{{ $pendingTasks->total() }} Tasks</span>
                    </div>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-blue-50 text-[10px] uppercase tracking-widest text-blue-400 font-bold">
                                    <th class="px-6 py-4 pl-8">Task Title</th>
                                    <th class="px-6 py-4">Due Date</th>
                                    <th class="px-6 py-4">Assignment</th>
                                    <th class="px-6 py-4">Priority</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-blue-50">
                                @foreach($pendingTasks as $task)
                                    @include('tasks.partials.registry-row', ['task' => $task, 'hoverColor' => 'blue'])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($pendingTasks->hasPages())
                        <div class="px-6 py-3 bg-blue-50/30 border-t border-blue-100">
                            {{ $pendingTasks->links() }}
                        </div>
                    @endif
                </div>
                @endif

                {{-- 3. COMPLETED SECTION --}}
                @if($completedTasks->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-emerald-200 mb-4 relative">
                    <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500"></div>
                    <div class="bg-emerald-50/50 px-6 py-4 border-b border-emerald-100 flex items-center justify-between">
                        <h3 class="text-emerald-900 font-black uppercase text-sm tracking-widest flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Completed / Done
                        </h3>
                        <span class="bg-emerald-200 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded-full">{{ $completedTasks->total() }} Tasks</span>
                    </div>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-emerald-50 text-[10px] uppercase tracking-widest text-emerald-500 font-bold">
                                    <th class="px-6 py-4 pl-8">Task Title</th>
                                    <th class="px-6 py-4">Due Date</th>
                                    <th class="px-6 py-4">Completed By</th>
                                    <th class="px-6 py-4">Priority</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-50">
                                @foreach($completedTasks as $task)
                                    @include('tasks.partials.registry-row', ['task' => $task, 'hoverColor' => 'emerald'])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($completedTasks->hasPages())
                        <div class="px-6 py-3 bg-emerald-50/30 border-t border-emerald-100">
                            {{ $completedTasks->links() }}
                        </div>
                    @endif
                </div>
                @endif

                {{-- 4. ON HOLD SECTION --}}
                @if($onHoldTasks->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-slate-200 mb-8 relative">
                    <div class="absolute top-0 left-0 w-1 h-full bg-slate-500"></div>
                    <div class="bg-slate-50/50 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-slate-900 font-black uppercase text-sm tracking-widest flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-500"></span>
                            On Hold
                        </h3>
                        <span class="bg-slate-200 text-slate-800 text-[10px] font-bold px-2.5 py-1 rounded-full">{{ $onHoldTasks->total() }} Tasks</span>
                    </div>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-50 text-[10px] uppercase tracking-widest text-slate-500 font-bold">
                                    <th class="px-6 py-4 pl-8">Task Title</th>
                                    <th class="px-6 py-4">Due Date</th>
                                    <th class="px-6 py-4">Assignment</th>
                                    <th class="px-6 py-4">Priority</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($onHoldTasks as $task)
                                    @include('tasks.partials.registry-row', ['task' => $task, 'hoverColor' => 'slate'])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($onHoldTasks->hasPages())
                        <div class="px-6 py-3 bg-slate-50/30 border-t border-slate-100">
                            {{ $onHoldTasks->links() }}
                        </div>
                    @endif
                </div>
                @endif

                {{-- 5. CANCELED SECTION --}}
                @if($canceledTasks->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-red-200 mb-4 relative">
                    <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
                    <div class="bg-red-50/50 px-6 py-4 border-b border-red-100 flex items-center justify-between">
                        <h3 class="text-red-900 font-black uppercase text-sm tracking-widest flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            Canceled / Aborted
                        </h3>
                        <span class="bg-red-200 text-red-800 text-[10px] font-bold px-2.5 py-1 rounded-full">{{ $canceledTasks->total() }} Tasks</span>
                    </div>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-red-50 text-[10px] uppercase tracking-widest text-red-500 font-bold">
                                    <th class="px-6 py-4 pl-8">Task Title</th>
                                    <th class="px-6 py-4">Due Date</th>
                                    <th class="px-6 py-4">Assignment</th>
                                    <th class="px-6 py-4">Priority</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-red-50">
                                @foreach($canceledTasks as $task)
                                    @include('tasks.partials.registry-row', ['task' => $task, 'hoverColor' => 'red'])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($canceledTasks->hasPages())
                        <div class="px-6 py-3 bg-red-50/30 border-t border-red-100">
                            {{ $canceledTasks->links() }}
                        </div>
                    @endif
                </div>
                @endif

            @endif

        </div>

        {{-- TASK DETAILS MODAL (Pop-up) --}}
        <div x-show="showModal" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                 @click="showModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showModal"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-200 flex flex-col max-h-[90vh]">
                        
                        {{-- Modal Header --}}
                        <div class="px-6 py-4 flex justify-between items-center border-b border-gray-100 bg-gray-50/50">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg shadow-sm" :class="modalData.priority === 'High' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </div>
                                <h3 class="text-lg font-black text-gray-800 uppercase tracking-wide">Task Information</h3>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 text-[10px] font-bold uppercase rounded-full border shadow-sm"
                                      :class="modalData.priority === 'High' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200'"
                                      x-text="modalData.priority">
                                </span>
                                <button @click="showModal = false" class="text-gray-400 hover:text-gray-700 hover:bg-gray-200 transition rounded-full p-1.5 focus:outline-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Modal Body --}}
                        <div class="px-6 py-6 overflow-y-auto flex-1 custom-scrollbar">
                            
                            {{-- Title Section --}}
                            <div class="mb-6">
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Task Title
                                </h4>
                                <p class="text-xl font-bold text-gray-900 leading-tight" x-text="modalData.title"></p>
                            </div>

                            {{-- Description Section --}}
                            <div class="mb-6">
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                    Description
                                </h4>
                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 shadow-inner max-h-48 overflow-y-auto custom-scrollbar">
                                    {{-- Uses x-html for Rich Text TinyMCE rendering --}}
                                    <div class="text-sm text-slate-700 leading-relaxed tinymce-content" x-html="modalData.description"></div>
                                </div>
                            </div>

                            {{-- Details Grid --}}
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

                        {{-- Modal Footer --}}
                        <div class="bg-gray-50/80 px-6 py-4 flex justify-end border-t border-gray-100 rounded-b-2xl">
                            <button @click="showModal = false" type="button" class="inline-flex justify-center rounded-xl bg-white px-6 py-2.5 text-sm font-bold text-gray-700 shadow-sm border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition-all w-auto">
                                Close Window
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>