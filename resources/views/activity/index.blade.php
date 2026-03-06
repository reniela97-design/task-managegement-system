<x-app-layout>
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                {{ __('Activity Logs') }}
            </h2>
            <div class="text-sm font-medium text-gray-500">
                System Audit Trail
            </div>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Account Filter (Only visible to Admins and Managers) --}}
            @if(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager'))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between shadow-sm">
                <span class="text-blue-900 font-bold uppercase text-xs tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Personnel Filter:
                </span>
                <form method="GET" action="{{ route('activity.index') }}">
                    <select name="user_id" class="text-sm rounded border-gray-300 focus:border-blue-900 focus:ring-blue-900 cursor-pointer" onchange="this.form.submit()">
                        <option value="">-- View All Personnel --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->user_id }}" {{ request('user_id') == $u->user_id ? 'selected' : '' }}>
                                {{ $u->user_name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            @endif

            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
                
                <div class="bg-blue-900 px-6 py-4 border-b border-blue-800 flex justify-between items-center">
                    <h3 class="text-white font-bold uppercase text-sm tracking-wide">Recent Activities</h3>
                    
                    @if(auth()->user()->hasRole('Administrator'))
                        <span class="bg-blue-800 text-blue-200 text-[10px] font-bold px-2 py-1 rounded border border-blue-700">ADMIN VIEW</span>
                    @elseif(auth()->user()->hasRole('Manager'))
                        <span class="bg-blue-800 text-blue-200 text-[10px] font-bold px-2 py-1 rounded border border-blue-700">MANAGER VIEW</span>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-bold">
                                <th class="px-6 py-4">Timestamp</th>
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Description</th>
                                <th class="px-6 py-4">Technical Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($activities as $log)
                            <tr class="hover:bg-blue-50 transition group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-700">
                                            {{ \Carbon\Carbon::parse($log->activity_log_datetime)->format('M d, Y') }}
                                        </span>
                                        <span class="text-xs text-gray-400 font-mono">
                                            {{ \Carbon\Carbon::parse($log->activity_log_datetime)->format('h:i:s A') }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs border border-white shadow-sm">
                                            {{ substr($log->user->user_name ?? '?', 0, 1) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-blue-900 text-xs uppercase">
                                                {{ $log->user->user_name ?? 'Unknown' }}
                                            </span>
                                            <span class="text-[10px] text-gray-500">
                                                {{ $log->user->role->role_name ?? 'No Role' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                                    {{ $log->activity_description }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col text-[10px] text-gray-400">
                                        @if($log->activity_ip_address)
                                            <span class="font-mono">IP: {{ $log->activity_ip_address }}</span>
                                        @endif
                                        @if($log->activity_agent)
                                            <span class="truncate w-32" title="{{ $log->activity_agent }}">
                                                {{ Str::limit($log->activity_agent, 20) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    No activities recorded yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($activities->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $activities->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>