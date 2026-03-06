<x-app-layout>
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                {{ __('User Accounts') }}
            </h2>
            <div class="text-sm font-medium text-gray-500">
                Total Users: <span class="text-blue-900 font-bold">{{ $users->count() }}</span>
            </div>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Action Toolbar --}}
            <div class="flex justify-end">
                <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-3 px-6 rounded-full shadow-lg shadow-blue-900/30 transition uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Add New Account
                </a>
            </div>

            @if(session('status'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="text-green-700 text-sm font-bold">{{ session('status') }}</span>
                </div>
            @endif

            {{-- Group Logic --}}
            @php
                $groupedUsers = $users->groupBy(function($item) {
                    return $item->role ? $item->role->role_name : 'No Role Assigned';
                })->sortKeys();
            @endphp

            @foreach($groupedUsers as $roleName => $roleUsers)
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
                
                {{-- Role Header --}}
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center
                    {{ $roleName === 'Administrator' ? 'bg-purple-900 border-purple-800' : '' }}
                    {{ $roleName === 'Manager' ? 'bg-blue-900 border-blue-800' : '' }}
                    {{ !in_array($roleName, ['Administrator', 'Manager']) ? 'bg-gray-800 border-gray-700' : '' }}
                ">
                    <h3 class="text-white font-bold uppercase text-sm tracking-wide flex items-center gap-2">
                        @if($roleName === 'Administrator') 
                            <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        @elseif($roleName === 'Manager')
                            <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        @else
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        @endif
                        {{ $roleName }}s
                    </h3>
                    <span class="py-1 px-3 rounded text-xs font-bold
                        {{ $roleName === 'Administrator' ? 'bg-purple-800 text-purple-200' : '' }}
                        {{ $roleName === 'Manager' ? 'bg-blue-800 text-blue-200' : '' }}
                        {{ !in_array($roleName, ['Administrator', 'Manager']) ? 'bg-gray-700 text-gray-300' : '' }}
                    ">{{ $roleUsers->count() }} Accounts</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-bold">
                                <th class="px-6 py-4">User Identity</th>
                                <th class="px-6 py-4">Email Address</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($roleUsers as $user)
                            <tr class="hover:bg-blue-50 transition group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-full bg-white flex items-center justify-center text-gray-700 font-black text-xs border border-gray-200 shadow-sm">
                                            {{ substr($user->user_name, 0, 1) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-800 text-sm group-hover:text-blue-900 transition">{{ $user->user_name }}</span>
                                            @if($user->user_id === auth()->id())
                                                <span class="text-[10px] text-green-600 font-bold uppercase tracking-wider">(You)</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                    {{ $user->user_email }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        <a href="{{ route('users.edit', $user->user_id) }}" class="text-gray-400 hover:text-blue-600 transition p-2 hover:bg-blue-50 rounded-full" title="Edit User">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        
                                        @if($user->user_id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user->user_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to deactivate this account?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-600 transition p-2 hover:bg-red-50 rounded-full" title="Deactivate Account">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</x-app-layout>