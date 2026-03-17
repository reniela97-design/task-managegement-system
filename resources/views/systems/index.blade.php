<x-app-layout>
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                {{ __('Systems') }}
            </h2>
            
            <div class="flex items-center gap-3">
                <!-- Live Search with Label on Left - Clear button inside -->
                <div class="flex items-center bg-white border border-gray-300 rounded-lg overflow-hidden">
                    <span class="px-3 text-sm font-medium text-gray-600 bg-gray-50 py-1.5 border-r border-gray-300">Search:</span>
                    <div class="relative">
                        <input type="text" 
                               id="search-input" 
                               value="{{ request('search') }}" 
                               placeholder="" 
                               class="text-sm py-1.5 pl-3 pr-8 focus:outline-none w-48"
                               autocomplete="off">
                        
                        <!-- Clear button inside the input -->
                        <button id="clear-search" 
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-600 transition {{ request('search') ? '' : 'hidden' }}"
                                onclick="clearSearch()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <a href="{{ route('systems.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded shadow-md transition text-xs uppercase tracking-wide">
                    + Add System
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages with Fade Out -->
    @if(session('success'))
        <div id="success-message" class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8 transition-opacity duration-1000">
            @if(str_contains(session('success'), 'deleted'))
                <!-- Delete success message in red -->
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @else
                <!-- Other success messages in green -->
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
        </div>
    @endif

    @if(session('error'))
        <div id="error-message" class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8 transition-opacity duration-1000">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600" id="systems-table">
                        <thead class="bg-gray-50 text-gray-900 uppercase font-bold text-xs border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">System Name</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="table-body">
                            @forelse ($systems as $system)
                                <tr class="hover:bg-gray-50 transition system-row" data-name="{{ strtolower($system->system_name) }}">
                                    <td class="px-6 py-4 font-medium">{{ $system->system_id }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-800 system-name">{{ $system->system_name }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end items-center gap-3">
                                            <a href="{{ route('systems.edit', $system->system_id) }}" class="text-gray-400 hover:text-blue-600 transition" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            
                                            <form method="POST" action="{{ route('systems.destroy', $system->system_id) }}" onsubmit="return confirm('Are you sure you want to delete this system?');" class="inline">
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
                            @empty
                                <tr id="no-results-row">
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">
                                        No systems found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for live search and fade out messages -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fade out success message after 2 seconds
            const successMsg = document.getElementById('success-message');
            if (successMsg) {
                setTimeout(() => {
                    successMsg.style.transition = 'opacity 1s';
                    successMsg.style.opacity = '0';
                    setTimeout(() => {
                        successMsg.style.display = 'none';
                    }, 1000);
                }, 2000);
            }

            // Fade out error message after 2 seconds
            const errorMsg = document.getElementById('error-message');
            if (errorMsg) {
                setTimeout(() => {
                    errorMsg.style.transition = 'opacity 1s';
                    errorMsg.style.opacity = '0';
                    setTimeout(() => {
                        errorMsg.style.display = 'none';
                    }, 1000);
                }, 2000);
            }

            // Live search functionality
            const searchInput = document.getElementById('search-input');
            const tableBody = document.getElementById('table-body');
            const systemRows = document.querySelectorAll('.system-row');
            const noResultsRow = document.getElementById('no-results-row');
            const clearButton = document.getElementById('clear-search');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    // Show/hide clear button
                    if (searchTerm.length > 0) {
                        clearButton.classList.remove('hidden');
                    } else {
                        clearButton.classList.add('hidden');
                    }
                    
                    let hasResults = false;
                    
                    systemRows.forEach(row => {
                        const systemName = row.getAttribute('data-name');
                        if (systemName.includes(searchTerm)) {
                            row.style.display = '';
                            hasResults = true;
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    
                    // Show/hide no results message
                    if (noResultsRow) {
                        if (!hasResults && systemRows.length > 0) {
                            noResultsRow.style.display = '';
                        } else {
                            noResultsRow.style.display = 'none';
                        }
                    }
                });

                // Trigger input event on page load if there's a search value
                if (searchInput.value) {
                    searchInput.dispatchEvent(new Event('input'));
                }
            }
        });

        // Clear search function
        function clearSearch() {
            const searchInput = document.getElementById('search-input');
            const clearButton = document.getElementById('clear-search');
            
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
                searchInput.dispatchEvent(new Event('input'));
            }
            
            if (clearButton) {
                clearButton.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>