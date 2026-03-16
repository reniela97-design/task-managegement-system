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
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-100 text-indigo-700 rounded-lg shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl text-indigo-900 uppercase tracking-tight">
                        {{ __('Task Overview') }}
                    </h2>
                    <p class="text-sm font-medium text-gray-500">Detailed information and current status.</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('gantt.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2 px-5 rounded-xl shadow-sm transition uppercase tracking-wide text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to List
                </a>
                
                {{-- Edit Button logic: Admin/Manager OR the Assigned User can edit --}}
                @if(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager') || auth()->id() == $task->task_assign_to)
                    <a href="{{ route('tasks.edit', $task->task_id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-5 rounded-xl shadow-sm transition uppercase tracking-wide text-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Task
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- PENDING EDIT APPROVAL WARNING --}}
            @if($task->task_edit_pending)
                <div class="mb-6 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-start gap-4 shadow-sm">
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- LEFT COLUMN: Main Content (Title, Description, Remarks) --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Title & Description Card --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-8">
                        <div class="flex items-start justify-between gap-4 mb-6">
                            <h3 class="text-3xl font-black text-gray-900 leading-tight">{{ $task->task_title }}</h3>
                        </div>

                        <div class="border-t border-gray-100 pt-6">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                Task Description
                            </h4>
                            <div class="bg-slate-50/50 rounded-xl p-6 border border-slate-100 text-slate-700 tinymce-content">
                                {{-- Use {!! !!} to properly render the TinyMCE HTML --}}
                                @if($task->task_description)
                                    {!! $task->task_description !!}
                                @else
                                    <span class="italic text-gray-400">No description provided.</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Remarks Card --}}
                    @if($task->task_remarks)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-8">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                            Closing Remarks / Resolution
                        </h4>
                        <div class="bg-amber-50/30 rounded-xl p-6 border border-amber-100 text-slate-700 tinymce-content">
                            {{-- Use {!! !!} to properly render the TinyMCE HTML --}}
                            {!! $task->task_remarks !!}
                        </div>
                    </div>
                    @endif

                </div>

                {{-- RIGHT COLUMN: Metadata Sidebar --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-6">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 border-b border-gray-100 pb-3">Metadata</h4>
                        
                        <div class="space-y-6">
                            {{-- Priority & Status Badges --}}
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-[10px] font-bold border uppercase tracking-widest {{ $task->task_priority_id == 1 ? 'bg-red-50 text-red-700 border-red-200' : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                    {{ $task->task_priority_id == 1 ? '🔥 Emergency' : 'Normal Priority' }}
                                </span>
                                
                                @if($task->task_status_id == 3)
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-widest">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Completed
                                    </span>
                                @elseif($task->task_status_id == 2)
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-widest">
                                        <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        In Progress
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-widest">
                                        Pending
                                    </span>
                                @endif
                            </div>

                            {{-- Assignee --}}
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">{{ $task->task_status_id == 3 ? 'Completed By' : 'Assigned To' }}</label>
                                @if($task->assignee)
                                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm shadow-sm border border-indigo-200">
                                            {{ substr($task->assignee->user_name, 0, 1) }}
                                        </div>
                                        <p class="font-bold text-gray-800 text-sm">{{ $task->assignee->user_name }}</p>
                                    </div>
                                @else
                                    <div class="flex items-center gap-3 bg-red-50 p-3 rounded-xl border border-red-100">
                                        <div class="h-8 w-8 rounded-full bg-red-200 text-red-600 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </div>
                                        <p class="font-bold text-red-700 text-sm">Unassigned</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Project & Client --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Project</label>
                                    <p class="font-bold text-sm text-gray-800 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                        {{ $task->project->project_name ?? 'General' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Client</label>
                                    <p class="font-bold text-sm text-gray-800 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $task->client->client_name ?? 'Internal' }}
                                    </p>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            {{-- Dates --}}
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 font-medium">Created On</span>
                                    <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($task->task_log_datetime)->format('M d, Y') }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 font-medium">Target Due</span>
                                    <span class="font-bold {{ $task->task_due_date && \Carbon\Carbon::parse($task->task_due_date)->isPast() && $task->task_status_id != 3 ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ $task->task_due_date ? \Carbon\Carbon::parse($task->task_due_date)->format('M d, Y') : 'No Date' }}
                                    </span>
                                </div>

                                @if($task->task_date_start)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 font-medium">Started Work</span>
                                    <span class="font-bold text-indigo-600">{{ \Carbon\Carbon::parse($task->task_date_start)->format('M d, Y') }}</span>
                                </div>
                                @endif

                                @if($task->task_date_end && $task->task_status_id == 3)
                                <div class="flex items-center justify-between text-sm bg-emerald-50 p-2 rounded-lg border border-emerald-100 mt-2">
                                    <span class="text-emerald-700 font-bold text-xs uppercase tracking-widest">Finished</span>
                                    <span class="font-black text-emerald-700">{{ \Carbon\Carbon::parse($task->task_date_end)->format('M d, Y') }}</span>
                                </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>