<x-app-layout>
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                {{ __('Projects') }}
            </h2>
            <div class="text-sm font-medium text-gray-500">
                Total Projects: <span class="text-blue-900 font-bold">{{ $projects->count() }}</span>
            </div>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
                
                <div class="bg-blue-900 px-6 py-4 border-b border-blue-800 flex flex-col md:flex-row justify-between items-center gap-4">
                    <h3 class="text-white font-bold uppercase text-sm tracking-wide">Project Master List</h3>
                    
                    <div class="flex items-center gap-3">
                        <form method="GET" action="{{ route('projects.index') }}" class="flex items-center gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects..." class="text-sm rounded border-blue-800 bg-blue-800/50 text-white placeholder-blue-300 focus:ring-white focus:border-white py-1.5 px-3 w-48 md:w-64" />
                            <button type="submit" class="bg-blue-800 hover:bg-blue-700 text-white font-bold py-1.5 px-3 rounded transition text-xs uppercase tracking-wide border border-blue-700">Filter</button>
                            @if(request('search'))
                                <a href="{{ route('projects.index') }}" class="text-blue-300 hover:text-white text-[10px] font-bold uppercase">Clear</a>
                            @endif
                        </form>

                        @if(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager'))
                        <a href="{{ route('projects.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-2 px-4 rounded shadow-lg shadow-blue-900/50 transition uppercase tracking-wide flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            New Project
                        </a>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-bold">
                                <th class="px-6 py-4">Project Name</th>
                                <th class="px-6 py-4">Client</th>
                                <th class="px-6 py-4">Location / Address</th>
                                @if(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager'))
                                <th class="px-6 py-4 text-right">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($projects as $project)
                            <tr class="hover:bg-blue-50 transition group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded bg-blue-100 flex items-center justify-center text-blue-800 font-bold text-xs border border-blue-200">
                                            {{ substr($project->project_name, 0, 1) }}
                                        </div>
                                        <span class="font-bold text-gray-800 text-sm group-hover:text-blue-900 transition">{{ $project->project_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($project->client)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            {{ $project->client->client_name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic text-xs">No Client</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $project->project_address ?? '-' }}
                                </td>
                                
                                @if(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager'))
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-3">
                                        <a href="{{ route('projects.edit', $project->project_id) }}" class="text-gray-400 hover:text-blue-600 transition" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2-2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        
                                        <form action="{{ route('projects.destroy', $project->project_id) }}" method="POST" onsubmit="return confirm('Delete this project?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    No projects found.
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