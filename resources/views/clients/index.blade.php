<x-app-layout>
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                {{ __('Clients Management') }}
            </h2>
            <div class="text-sm font-medium text-gray-500">
                Total Clients: <span class="text-blue-900 font-bold" id="client-count">{{ $clients->count() }}</span>
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

    @if(session('duplicate_error'))
        <div id="duplicate-error-message" class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8 transition-opacity duration-1000">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('duplicate_error') }}</span>
            </div>
        </div>
    @endif

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
                
                <div class="bg-blue-900 px-6 py-4 border-b border-blue-800 flex flex-col md:flex-row justify-between items-center gap-4">
                    <h3 class="text-white font-bold uppercase text-sm tracking-wide">Client Directory</h3>
                    
                    <div class="flex items-center gap-3">
                        <!-- Live Search with Label on Left - Blue Theme -->
                        <div class="flex items-center bg-blue-800 border border-blue-700 rounded-lg overflow-hidden">
                            <span class="px-3 text-sm font-medium text-blue-200 bg-blue-800 py-1.5 border-r border-blue-700">Search:</span>
                            <div class="relative">
                                <input type="text" 
                                       id="search-input" 
                                       value="{{ request('search') }}" 
                                       placeholder="" 
                                       class="text-sm py-1.5 pl-3 pr-8 focus:outline-none bg-blue-800 text-white placeholder-blue-300 w-48"
                                       autocomplete="off">
                                
                                <!-- Clear button inside the input -->
                                <button id="clear-search" 
                                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-blue-300 hover:text-white transition {{ request('search') ? '' : 'hidden' }}"
                                        onclick="clearSearch()">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <a href="{{ route('clients.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-2 px-4 rounded shadow-lg shadow-blue-900/50 transition uppercase tracking-wide flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            New Client
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="clients-table">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-bold">
                                <th class="px-6 py-4">Client Name</th>
                                <th class="px-6 py-4">Contact Person</th>
                                <th class="px-6 py-4">Contact Number</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="table-body">
                            @forelse($clients as $client)
                            <tr class="hover:bg-blue-50 transition group client-row" 
                                data-name="{{ strtolower($client->client_name) }}"
                                data-contact="{{ strtolower($client->client_contact_person ?? '') }}"
                                data-number="{{ strtolower($client->client_contact_number ?? '') }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-800 font-bold text-xs border border-blue-200">
                                            {{ substr($client->client_name, 0, 1) }}
                                        </div>
                                        <span class="font-bold text-gray-800 text-sm group-hover:text-blue-900 transition client-name">{{ $client->client_name }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 font-medium">
                                        {{ $client->client_contact_person ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($client->client_contact_number)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200 font-mono">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                            {{ $client->client_contact_number }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic text-xs">No Contact</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-3">
                                        <a href="{{ route('clients.edit', $client->client_id) }}" class="text-gray-400 hover:text-blue-600 transition" title="Edit Client">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        
                                        <form action="{{ route('clients.destroy', $client->client_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this client?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition" title="Delete Client">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="no-results-row">
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        <span>No clients found. Add one to get started.</span>
                                    </div>
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

            // Fade out duplicate error message after 2 seconds
            const duplicateErrorMsg = document.getElementById('duplicate-error-message');
            if (duplicateErrorMsg) {
                setTimeout(() => {
                    duplicateErrorMsg.style.transition = 'opacity 1s';
                    duplicateErrorMsg.style.opacity = '0';
                    setTimeout(() => {
                        duplicateErrorMsg.style.display = 'none';
                    }, 1000);
                }, 2000);
            }

            // Live search functionality
            const searchInput = document.getElementById('search-input');
            const clientRows = document.querySelectorAll('.client-row');
            const noResultsRow = document.getElementById('no-results-row');
            const clearButton = document.getElementById('clear-search');
            const clientCountSpan = document.getElementById('client-count');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    // Show/hide clear button
                    if (searchTerm.length > 0) {
                        clearButton.classList.remove('hidden');
                    } else {
                        clearButton.classList.add('hidden');
                    }
                    
                    let visibleCount = 0;
                    
                    clientRows.forEach(row => {
                        const clientName = row.getAttribute('data-name');
                        const contactPerson = row.getAttribute('data-contact');
                        const contactNumber = row.getAttribute('data-number');
                        
                        if (clientName.includes(searchTerm) || 
                            contactPerson.includes(searchTerm) || 
                            contactNumber.includes(searchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    
                    // Update visible count
                    if (clientCountSpan) {
                        clientCountSpan.textContent = visibleCount;
                    }
                    
                    // Show/hide no results message
                    if (noResultsRow) {
                        if (visibleCount === 0 && clientRows.length > 0) {
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