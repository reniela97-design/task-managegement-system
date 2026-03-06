<x-app-layout>
    {{-- TinyMCE Rich Text Editor CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>

    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center gap-3">
            <div class="p-2 bg-indigo-100 text-indigo-700 rounded-lg shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <div>
                <h2 class="font-black text-2xl text-indigo-900 uppercase tracking-tight">
                    {{ __('Edit Task') }}
                </h2>
                <p class="text-sm font-medium text-gray-500">Update parameters for: <span class="font-bold text-gray-700">{{ $task->task_title }}</span></p>
            </div>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('tasks.update', $task->task_id) }}">
                @csrf
                @method('PUT')

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
                                <label for="task_title" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Task Title <span class="text-red-500">*</span></label>
                                <input type="text" name="task_title" id="task_title" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg py-3 px-4 font-medium transition" value="{{ old('task_title', $task->task_title) }}" required autofocus>
                                <x-input-error :messages="$errors->get('task_title')" class="mt-2" />
                            </div>

                            {{-- Description with Word-like Editor --}}
                            <div>
                                <label for="task_description" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2 flex items-center justify-between">
                                    <span>Task Description</span>
                                    <span class="text-[10px] text-gray-400 font-normal normal-case tracking-normal">Use the toolbar to format your text</span>
                                </label>
                                <textarea name="task_description" id="task_description" class="hidden">{!! old('task_description', $task->task_description) !!}</textarea>
                                <x-input-error :messages="$errors->get('task_description')" class="mt-2" />
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
                                    <label for="task_client_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Client Context</label>
                                    <select name="task_client_id" id="task_client_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                                        <option value="">-- Internal / No Client --</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->client_id }}" {{ old('task_client_id', $task->task_client_id) == $client->client_id ? 'selected' : '' }}>
                                                {{ $client->client_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="task_project_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Project Assignment</label>
                                    <select name="task_project_id" id="task_project_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                                        <option value="">-- General Workspace --</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->project_id }}" {{ old('task_project_id', $task->task_project_id) == $project->project_id ? 'selected' : '' }}>
                                                {{ $project->project_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-indigo-50/50 p-6 rounded-xl border border-indigo-100">
                                <div>
                                    <label for="task_system_id" class="block text-[10px] font-bold text-indigo-800 uppercase tracking-widest mb-2">System</label>
                                    <select name="task_system_id" id="task_system_id" class="block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                                        <option value="">-- Select System --</option>
                                        @foreach($systems as $sys)
                                            <option value="{{ $sys->system_id }}" {{ old('task_system_id', $task->task_system_id) == $sys->system_id ? 'selected' : '' }}>
                                                {{ $sys->system_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="task_category_id" class="block text-[10px] font-bold text-indigo-800 uppercase tracking-widest mb-2">Category</label>
                                    <select name="task_category_id" id="task_category_id" class="block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->category_id }}" {{ old('task_category_id', $task->task_category_id) == $cat->category_id ? 'selected' : '' }}>
                                                {{ $cat->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="task_type_id" class="block text-[10px] font-bold text-indigo-800 uppercase tracking-widest mb-2">Task Type</label>
                                    <select name="task_type_id" id="task_type_id" class="block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                                        <option value="">-- Select Type --</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type->type_id }}" {{ old('task_type_id', $task->task_type_id) == $type->type_id ? 'selected' : '' }}>
                                                {{ $type->type_name }}
                                            </option>
                                        @endforeach
                                    </select>
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
                            
                            {{-- Admin/Manager Assignment --}}
                            @if(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager'))
                            <div class="lg:col-span-2">
                                <label for="task_assign_to" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Assign Personnel</label>
                                <select name="task_assign_to" id="task_assign_to" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5">
                                    <option value="">-- Unassigned --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->user_id }}" {{ old('task_assign_to', $task->task_assign_to) == $user->user_id ? 'selected' : '' }}>
                                            {{ $user->user_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('task_assign_to')" class="mt-2" />
                            </div>
                            @endif

                            <div class="{{ !(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager')) ? 'lg:col-span-2' : '' }}">
                                <label for="task_due_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2 flex items-center gap-1">
                                    Target Due Date
                                </label>
                                <input type="date" name="task_due_date" id="task_due_date" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5" 
                                       value="{{ old('task_due_date', $task->task_due_date ? \Carbon\Carbon::parse($task->task_due_date)->format('Y-m-d') : '') }}">
                                <x-input-error :messages="$errors->get('task_due_date')" class="mt-2" />
                            </div>

                            <div>
                                <label for="task_priority_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Priority <span class="text-red-500">*</span></label>
                                <select name="task_priority_id" id="task_priority_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 bg-red-50" required>
                                    @foreach($priorities as $priority)
                                        <option value="{{ $priority->priority_id }}" {{ old('task_priority_id', $task->task_priority_id) == $priority->priority_id ? 'selected' : '' }}>
                                            {{ $priority->priority_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="task_status_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Status <span class="text-red-500">*</span></label>
                                <select name="task_status_id" id="task_status_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5" required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->status_id }}" {{ old('task_status_id', $task->task_status_id) == $status->status_id ? 'selected' : '' }}>
                                            {{ $status->status_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: TIMELINE ADJUSTMENTS --}}
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100 mt-8">
                        <div class="bg-slate-50 px-8 py-4 border-b border-slate-100">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Execution Timeline (Optional Adjustments)
                            </h3>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b pb-2">Start Record</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Start Date</label>
                                        <input type="date" name="task_date_start" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" 
                                               value="{{ old('task_date_start', $task->task_date_start ? \Carbon\Carbon::parse($task->task_date_start)->format('Y-m-d') : '') }}">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Start Time</label>
                                        <input type="time" name="task_time_start" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" 
                                               value="{{ old('task_time_start', $task->task_time_start ? \Carbon\Carbon::parse($task->task_time_start)->format('H:i') : '') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b pb-2">Completion Record</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Finish Date</label>
                                        <input type="date" name="task_date_end" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" 
                                               value="{{ old('task_date_end', $task->task_date_end ? \Carbon\Carbon::parse($task->task_date_end)->format('Y-m-d') : '') }}">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Finish Time</label>
                                        <input type="time" name="task_time_end" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" 
                                               value="{{ old('task_time_end', $task->task_time_end ? \Carbon\Carbon::parse($task->task_time_end)->format('H:i') : '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 4: REMARKS --}}
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100 mt-8">
                        <div class="bg-slate-50 px-8 py-4 border-b border-slate-100">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                Task Remarks & Resolution
                            </h3>
                        </div>
                        <div class="p-8">
                            <label for="task_remarks" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2 flex items-center justify-between">
                                <span>Closing Remarks / Notes</span>
                                <span class="text-[10px] text-gray-400 font-normal normal-case tracking-normal">Optional details, blockers, or resolutions</span>
                            </label>
                            <textarea name="task_remarks" id="task_remarks" class="hidden">{!! old('task_remarks', $task->task_remarks) !!}</textarea>
                            <x-input-error :messages="$errors->get('task_remarks')" class="mt-2" />
                        </div>
                    </div>

                    {{-- FORM ACTIONS --}}
                    <div class="flex items-center justify-end gap-4 mt-8 pb-8">
                        <a href="{{ route('tasks.index') }}" class="text-gray-500 hover:text-gray-800 text-xs font-bold uppercase tracking-widest transition px-4 py-2">
                            Cancel Changes
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-8 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 uppercase tracking-wider text-sm flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Save Updates
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Initialize TinyMCE Editor for BOTH Textareas --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#task_description, #task_remarks', // Targets both Description and Remarks
                height: 300,
                menubar: false,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks fontfamily fontsize | ' +
                    'bold italic underline strikethrough | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help',
                content_style: 'body { font-family: "Instrument Sans", sans-serif; font-size: 14px; color: #334155; }',
                skin: 'oxide',
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save(); 
                    });
                }
            });
        });
    </script>
</x-app-layout>