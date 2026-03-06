<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Categories Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <form method="GET" action="{{ route('categories.index') }}" class="flex items-center gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                        <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded text-sm">Filter</button>
                        @if(request('search'))
                            <a href="{{ route('categories.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Clear</a>
                        @endif
                    </form>

                    <a href="{{ route('categories.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        + New Category
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm font-light text-gray-900 dark:text-gray-100">
                        <thead class="border-b bg-gray-50 dark:bg-gray-700 font-medium">
                            <tr>
                                <th class="px-6 py-4">Category Name</th>
                                <th class="px-6 py-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 font-bold">{{ $category->category_name }}</td>
                                <td class="px-6 py-4 flex gap-3">
                                    <a href="{{ route('categories.edit', $category->category_id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    <form action="{{ route('categories.destroy', $category->category_id) }}" method="POST" onsubmit="return confirm('Delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No categories found.
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