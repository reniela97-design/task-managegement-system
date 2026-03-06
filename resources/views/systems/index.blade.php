<x-app-layout>
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                {{ __('Systems') }}
            </h2>
            
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('systems.index') }}" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search systems..." class="text-sm rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900 py-1.5 px-3">
                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-1.5 px-3 rounded-lg border border-gray-300 transition text-xs uppercase tracking-wide">Filter</button>
                    @if(request('search'))
                        <a href="{{ route('systems.index') }}" class="text-gray-500 hover:text-red-600 text-[10px] font-bold uppercase transition">Clear</a>
                    @endif
                </form>

                <a href="{{ route('systems.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded shadow-md transition text-xs uppercase tracking-wide">
                    + Add System
                </a>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-gray-900 uppercase font-bold text-xs border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">System Name</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($systems as $system)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium">{{ $system->system_id }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $system->system_name }}</td>
                                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                                        <a href="{{ route('systems.edit', $system->system_id) }}" class="text-blue-600 hover:text-blue-900 font-bold text-xs uppercase">Edit</a>
                                        <form method="POST" action="{{ route('systems.destroy', $system->system_id) }}" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-bold text-xs uppercase">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @if($systems->isEmpty())
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">
                                        No systems found.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>