<x-app-layout>
    {{-- HEADER --}}
    <div class="bg-white border-b border-gray-200 shadow-sm relative z-10">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-xl shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl text-gray-900 uppercase tracking-tight">Status Management</h2>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-0.5">System Status & Type Classifications</p>
                </div>
            </div>

            <a href="{{ route('statuses.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-sm transition text-[10px] uppercase tracking-widest flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                New Status
            </a>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status Name</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Color Label</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($statuses as $status)
                            <tr class="hover:bg-gray-50/80 transition-colors group">
                                <td class="px-6 py-4 font-black text-gray-800 text-base">
                                    {{ $status->status_name }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    
                                    {{-- FIX: Using {!! !!} to print the style attribute hides it from the VS Code CSS parser --}}
                                    <span class="px-4 py-1.5 rounded-lg text-white text-[10px] font-black shadow-sm uppercase tracking-widest"
                                          {!! 'style="background-color: ' . $status->status_color . ';"' !!}>
                                        {{ $status->status_name }}
                                    </span>
                                    
                                    <div class="text-[9px] text-gray-400 mt-1.5 font-mono font-bold uppercase">{{ $status->status_color }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('statuses.edit', $status->status_id) }}" class="text-[10px] font-bold uppercase tracking-widest text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-lg transition-colors">
                                            Edit
                                        </a>
                                        <form action="{{ route('statuses.destroy', $status->status_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this status?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[10px] font-bold uppercase tracking-widest text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-lg transition-colors">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center border border-gray-100 mx-auto mb-3 shadow-inner">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    </div>
                                    <p class="text-gray-900 font-bold text-sm">No statuses found</p>
                                    <p class="text-gray-500 text-xs mt-1">Click the button above to create your first status classification.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>