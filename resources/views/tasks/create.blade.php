<x-app-layout>
    {{-- TinyMCE Rich Text Editor CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>

    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center gap-3">
            <div class="p-2 bg-blue-100 text-blue-700 rounded-lg shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </div>
            <div>
                <h2 class="font-black text-2xl text-blue-900 uppercase tracking-tight">
                    {{ __('Create New Task') }}
                </h2>
                <p class="text-sm font-medium text-gray-500">Draft and assign a new system objective.</p>
            </div>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('tasks.store') }}">
                @csrf

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
                                <input type="text" name="task_title" id="task_title" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg py-3 px-4 font-medium transition" placeholder="e.g., Update Server Firewall Parameters" value="{{ old('task_title') }}" required autofocus>
                                <x-input-error :messages="$errors->get('task_title')" class="mt-2" />
                            </div>

                            {{-- Description with Word-like Editor --}}
                            <div>
                                <label for="task_description" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2 flex items-center justify-between">
                                    <span>Task Description</span>
                                    <span class="text-[10px] text-gray-400 font-normal normal-case tracking-normal">Use the toolbar to format your text</span>
                                </label>
                                <textarea name="task_description" id="task_description" class="hidden">{{ old('task_description') }}</textarea>
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
                                            <option value="{{ $client->client_id }}" {{ old('task_client_id') == $client->client_id ? 'selected' : '' }}>{{ $client->client_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="task_project_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Project Assignment</label>
                                    <select name="task_project_id" id="task_project_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                                        <option value="">-- General Workspace --</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->project_id }}" {{ old('task_project_id') == $project->project_id ? 'selected' : '' }}>{{ $project->project_name }}</option>
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
                                            <option value="{{ $sys->system_id }}" {{ old('task_system_id') == $sys->system_id ? 'selected' : '' }}>{{ $sys->system_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="task_category_id" class="block text-[10px] font-bold text-indigo-800 uppercase tracking-widest mb-2">Category</label>
                                    <select name="task_category_id" id="task_category_id" class="block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->category_id }}" {{ old('task_category_id') == $cat->category_id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="task_type_id" class="block text-[10px] font-bold text-indigo-800 uppercase tracking-widest mb-2">Task Type</label>
                                    <select name="task_type_id" id="task_type_id" class="block w-full rounded-lg border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2">
                                        <option value="">-- Select Type --</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type->type_id }}" {{ old('task_type_id') == $type->type_id ? 'selected' : '' }}>{{ $type->type_name }}</option>
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
                                    <option value="">-- Assign to Me (Default) --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->user_id }}" {{ old('task_assign_to') == $user->user_id ? 'selected' : '' }}>{{ $user->user_name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('task_assign_to')" class="mt-2" />
                            </div>
                            @endif

                            <div class="{{ !(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager')) ? 'lg:col-span-2' : '' }}">
                                <label for="task_due_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2 flex items-center gap-1">
                                    Target Due Date
                                </label>
                                <input type="date" name="task_due_date" id="task_due_date" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5" value="{{ old('task_due_date') }}">
                                <x-input-error :messages="$errors->get('task_due_date')" class="mt-2" />
                            </div>

                            <div>
                                <label for="task_priority_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Priority <span class="text-red-500">*</span></label>
                                <select name="task_priority_id" id="task_priority_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 bg-red-50" required>
                                    @foreach($priorities as $priority)
                                        <option value="{{ $priority->priority_id }}" {{ old('task_priority_id') == $priority->priority_id ? 'selected' : '' }}>{{ $priority->priority_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="task_status_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Status <span class="text-red-500">*</span></label>
                                <select name="task_status_id" id="task_status_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5" required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->status_id }}" {{ old('task_status_id', 1) == $status->status_id ? 'selected' : '' }}>{{ $status->status_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- FORM ACTIONS --}}
                    <div class="flex items-center justify-end gap-4 mt-8">
                        <a href="{{ route('tasks.index') }}" class="text-gray-500 hover:text-gray-800 text-xs font-bold uppercase tracking-widest transition px-4 py-2">
                            Cancel & Return
                        </a>
                        <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-3.5 px-8 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 uppercase tracking-wider text-sm flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Initialize Task
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Initialize TinyMCE Editor --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#task_description',
                height: 350,
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
                    // Sync the editor data back to the hidden textarea on every change
                    editor.on('change', function () {
                        editor.save(); 
                    });
                }
            });
        });
    </script>
</x-app-layout>