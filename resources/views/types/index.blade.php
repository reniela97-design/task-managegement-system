<x-app-layout>
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                {{ __('Types') }}
            </h2>
            
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('types.index') }}" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search types..." class="text-sm rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900 py-1.5 px-3">
                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-1.5 px-3 rounded-lg border border-gray-300 transition text-xs uppercase tracking-wide">Filter</button>
                    @if(request('search'))
                        <a href="{{ route('types.index') }}" class="text-gray-500 hover:text-red-600 text-[10px] font-bold uppercase transition">Clear</a>
                    @endif
                </form>

                <a href="{{ route('types.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded shadow-md transition text-xs uppercase tracking-wide">
                    + Add Type
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
                                <th class="px-6 py-4">Type Name</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($types as $type)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium">{{ $type->type_id }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $type->type_name }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end items-center gap-3">
                                            <a href="{{ route('types.edit', $type->type_id) }}" class="text-gray-400 hover:text-blue-600 transition" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            
                                            <form method="POST" action="{{ route('types.destroy', $type->type_id) }}" onsubmit="return confirm('Are you sure?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-600 transition" title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if($types->isEmpty())
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">
                                        No types found. Create one to get started.
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