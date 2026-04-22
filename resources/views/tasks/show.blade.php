<x-app-layout>
    <style>
        /* CSS to properly render TinyMCE styles inside Tailwind */
        .tinymce-content ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin-bottom: 0.75rem !important; }
        .tinymce-content ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin-bottom: 0.75rem !important; }
        .tinymce-content p { margin-bottom: 0.75rem !important; line-height: 1.6; }
        .tinymce-content strong, .tinymce-content b { font-weight: 700 !important; color: #1e293b; }
        .tinymce-content em, .tinymce-content i { font-style: italic !important; }
        .tinymce-content a { color: #3b82f6 !important; text-decoration: underline !important; transition: color 0.2s; }
        .tinymce-content a:hover { color: #1d4ed8 !important; }
        .tinymce-content h1, .tinymce-content h2, .tinymce-content h3 { font-weight: bold !important; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #0f172a; }
    </style>

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 shadow-sm relative z-10">
        <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-100 text-indigo-700 rounded-lg shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl text-indigo-900 uppercase tracking-tight">
                        {{ __('Task Overview') }}
                    </h2>
                    <p class="text-sm font-medium text-gray-500">Read-only view of the task details and parameters.</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                {{-- Dynamic Back Button --}}
                <a href="javascript:history.back()" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2.5 px-5 rounded-xl shadow-sm transition uppercase tracking-wide text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Go Back
                </a>
            </div>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            {{-- PENDING EDIT APPROVAL WARNING --}}
            @if($task->task_edit_pending)
                <div class="mb-8 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-start gap-4 shadow-sm">
                    <div class="p-2 bg-amber-100 rounded-full text-amber-600 shrink-0 mx-auto sm:mx-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <h3 class="text-sm font-bold text-amber-800 uppercase tracking-widest">Changes Pending Approval</h3>
                        <p class="text-sm text-amber-700 mt-1">A user has submitted edits to this task. The information below reflects the <strong>current live data</strong>. Awaiting administrator review.</p>
                    </div>
                    @if(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager'))
                        <div class="flex gap-2 shrink-0 mt-3 sm:mt-0 justify-center">
                            <form action="{{ route('tasks.approve', $task->task_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-4 rounded-lg shadow transition">Approve Changes</button>
                            </form>
                            <form action="{{ route('tasks.reject', $task->task_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-bold py-2 px-4 rounded-lg shadow transition">Reject</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif

            <div class="space-y-8">
                
                {{-- SECTION 1: MAIN INFO --}}
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100">
                    <div class="bg-slate-50 px-8 py-4 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Basic Information
                        </h3>
                    </div>
                    <div class="p-8 space-y-6">
                        {{-- Title --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Task Title</label>
                            <div class="block w-full rounded-xl border border-gray-200 bg-gray-50/50 text-lg py-3 px-4 font-black text-gray-900">
                                {{ $task->task_title }}
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Task Description</label>
                            <div class="block w-full rounded-xl border border-gray-200 bg-white shadow-inner p-6 min-h-[150px]">
                                <div class="tinymce-content text-slate-700">
                                    @if($task->task_description)
                                        {!! $task->task_description !!}
                                    @else
                                        <span class="italic text-gray-400">No description provided.</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: CATEGORIZATION --}}
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100">
                    <div class="bg-slate-50 px-8 py-4 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            Task Categorization
                        </h3>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Client Context</label>
                                <div class="block w-full rounded-xl border border-gray-200 bg-gray-50/50 text-sm py-3 px-4 font-bold text-gray-800">
                                    {{ $task->client->client_name ?? 'Internal / No Client' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Project Assignment</label>
                                <div class="block w-full rounded-xl border border-gray-200 bg-gray-50/50 text-sm py-3 px-4 font-bold text-gray-800">
                                    {{ $task->project->project_name ?? 'General Workspace' }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-indigo-50/50 p-6 rounded-xl border border-indigo-100">
                            <div>
                                <label class="block text-[10px] font-bold text-indigo-800 uppercase tracking-widest mb-2">System</label>
                                <div class="block w-full rounded-lg border border-indigo-200 bg-white text-sm py-2 px-3 font-bold text-indigo-900">
                                    {{ $task->system->system_name ?? 'N/A' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-indigo-800 uppercase tracking-widest mb-2">Category</label>
                                <div class="block w-full rounded-lg border border-indigo-200 bg-white text-sm py-2 px-3 font-bold text-indigo-900">
                                    {{ $task->category->category_name ?? 'N/A' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-indigo-800 uppercase tracking-widest mb-2">Task Type</label>
                                <div class="block w-full rounded-lg border border-indigo-200 bg-white text-sm py-2 px-3 font-bold text-indigo-900">
                                    {{ $task->type->type_name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: EXECUTION & STATUS --}}
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100">
                    <div class="bg-slate-50 px-8 py-4 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Execution & Assignment
                        </h3>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Assigned Personnel</label>
                            <div class="block w-full rounded-xl border border-gray-200 bg-gray-50/50 text-sm py-3 px-4 font-bold text-gray-900 flex items-center gap-2">
                                @if($task->assignee)
                                    <div class="h-5 w-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px]">
                                        {{ substr($task->assignee->user_name, 0, 1) }}
                                    </div>
                                    {{ $task->assignee->user_name }}
                                @else
                                    <span class="text-gray-400">Unassigned</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Target Due Date</label>
                            <div class="block w-full rounded-xl border border-gray-200 bg-gray-50/50 text-sm py-3 px-4 font-bold {{ $task->task_due_date && \Carbon\Carbon::parse($task->task_due_date)->isPast() && $task->task_status_id != 3 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $task->task_due_date ? \Carbon\Carbon::parse($task->task_due_date)->format('M d, Y') : 'No Date Set' }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Priority</label>
                            <div class="block w-full rounded-xl border text-sm py-3 px-4 font-black uppercase tracking-widest {{ $task->task_priority_id == 1 ? 'border-red-200 bg-red-50 text-red-700' : 'border-gray-200 bg-gray-50/50 text-gray-600' }}">
                                {{ $task->task_priority_id == 1 ? '🔥 High' : 'Normal' }}
                            </div>
                        </div>

                        <div class="lg:col-span-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Current Status</label>
                            <div class="block w-full rounded-xl border text-sm py-3 px-4 font-black uppercase tracking-widest
                                {{ $task->task_status_id == 3 ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 
                                  ($task->task_status_id == 2 ? 'border-indigo-200 bg-indigo-50 text-indigo-700' : 'border-blue-200 bg-blue-50 text-blue-700') }}">
                                {{ $task->status->status_name ?? 'Unknown' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 4: TIMELINE & REMARKS (Unique to Show View) --}}
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100">
                    <div class="bg-slate-50 px-8 py-4 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Timeline & Resolution
                        </h3>
                    </div>
                    <div class="p-8 space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Created On</label>
                                <div class="text-sm font-bold text-gray-800">
                                    {{ \Carbon\Carbon::parse($task->task_log_datetime)->format('M d, Y') }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Started On</label>
                                <div class="text-sm font-bold text-gray-800">
                                    {{ $task->task_date_start ? \Carbon\Carbon::parse($task->task_date_start)->format('M d, Y') : '—' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-2">Completed On</label>
                                <div class="text-sm font-bold text-emerald-700">
                                    {{ $task->task_date_end && $task->task_status_id == 3 ? \Carbon\Carbon::parse($task->task_date_end)->format('M d, Y') : '—' }}
                                </div>
                            </div>
                        </div>

                        @if($task->task_remarks)
                        <div class="border-t border-gray-100 pt-6">
                            <label class="block text-xs font-bold text-amber-700 uppercase tracking-wide mb-2">Closing Remarks / Resolution</label>
                            <div class="block w-full rounded-xl border border-amber-200 bg-amber-50/30 shadow-inner p-6">
                                <div class="tinymce-content text-slate-700">
                                    {!! $task->task_remarks !!}
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>