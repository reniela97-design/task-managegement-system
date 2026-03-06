<x-app-layout>
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                {{ __('System Configuration') }}
            </h2>
            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                Role Management
            </span>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- LEFT COLUMN: FORM --}}
                <div class="md:col-span-2">
                    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
                        <div class="bg-blue-900 px-6 py-4 border-b border-blue-800 flex justify-between items-center">
                            <h3 class="text-white font-bold uppercase text-sm tracking-wide flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Create New Role
                            </h3>
                            <a href="{{ route('roles.index') }}" class="text-blue-200 hover:text-white text-xs font-bold uppercase transition">
                                &larr; Cancel
                            </a>
                        </div>

                        <div class="p-8">
                            <form method="POST" action="{{ route('roles.store') }}">
                                @csrf
                                
                                <div class="mb-6">
                                    <x-input-label for="role_name" :value="__('Role Designation')" class="text-blue-900 font-bold uppercase text-xs tracking-wider mb-2" />
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </div>
                                        <x-text-input id="role_name" class="block mt-1 w-full pl-10 rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900" type="text" name="role_name" required autofocus placeholder="e.g. Compliance Officer" />
                                    </div>
                                    <x-input-error :messages="$errors->get('role_name')" class="mt-2" />
                                    <p class="text-xs text-gray-400 mt-2 italic">This name will be used throughout the system for permission checks.</p>
                                </div>

                                <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg shadow-blue-500/30 transition uppercase tracking-wide text-xs flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        {{ __('Save Configuration') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: GUIDELINES --}}
                <div class="md:col-span-1">
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 shadow-sm sticky top-24">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-blue-200/50">
                            <div class="bg-blue-900 text-white p-2 rounded-lg shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-blue-900 font-bold uppercase text-xs tracking-wider">Role Guidelines</h3>
                                <p class="text-[10px] text-blue-600 font-medium">Standard Operating Procedure</p>
                            </div>
                        </div>
                        
                        <div class="space-y-5 text-sm text-slate-600">
                            
                            {{-- Guideline 1 --}}
                            <div class="flex gap-3">
                                <span class="text-blue-400 font-black text-lg opacity-50">01</span>
                                <div>
                                    <h4 class="font-bold text-blue-800 text-xs uppercase mb-1">Naming Convention</h4>
                                    <p class="leading-relaxed text-xs text-slate-500">
                                        Use specific, functional titles. Avoid generic names like "Staff" if "Junior Auditor" is more accurate.
                                    </p>
                                </div>
                            </div>

                            {{-- Guideline 2 --}}
                            <div class="flex gap-3">
                                <span class="text-blue-400 font-black text-lg opacity-50">02</span>
                                <div>
                                    <h4 class="font-bold text-blue-800 text-xs uppercase mb-1">Hierarchy & Access</h4>
                                    <p class="leading-relaxed text-xs text-slate-500 mb-2">
                                        Roles define system visibility:
                                    </p>
                                    <ul class="space-y-1">
                                        <li class="flex items-center gap-2 text-[10px] uppercase font-bold text-slate-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Administrator
                                        </li>
                                        <li class="flex items-center gap-2 text-[10px] uppercase font-bold text-slate-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Manager
                                        </li>
                                        <li class="flex items-center gap-2 text-[10px] uppercase font-bold text-slate-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Standard User
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            {{-- Guideline 3 --}}
                            <div class="flex gap-3">
                                <span class="text-blue-400 font-black text-lg opacity-50">03</span>
                                <div>
                                    <h4 class="font-bold text-blue-800 text-xs uppercase mb-1">Unique Constraint</h4>
                                    <p class="leading-relaxed text-xs text-slate-500">
                                        Duplicate role names are not permitted. Ensure the designation does not already exist in the registry.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>