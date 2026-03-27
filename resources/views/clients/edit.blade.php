<x-app-layout>
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                {{ __('Edit Client') }}
            </h2>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
                <div class="bg-blue-900 px-6 py-4 border-b border-blue-800 flex justify-between items-center">
                    <h3 class="text-white font-bold uppercase text-sm tracking-wide">
                        Editing: <span class="text-blue-200">{{ $client->client_name }}</span>
                    </h3>
                    <a href="{{ route('clients.index') }}" class="text-blue-200 hover:text-white text-xs font-bold uppercase transition">
                        &larr; Back to List
                    </a>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('clients.update', $client->client_id) }}">
                        @csrf
                        @method('PATCH')
                        
                        <!-- Client/Company Name Field - Required -->
                        <div class="mb-6">
                            <x-input-label for="client_name" :value="__('Client / Company Name')" class="text-blue-900 font-bold uppercase text-xs tracking-wider mb-2" />
                            <x-text-input id="client_name" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900" type="text" name="client_name" value="{{ old('client_name', $client->client_name) }}" required autofocus />
                            
                            <!-- Error message for empty field -->
                            @error('client_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            
                            <!-- Error message for duplicate client -->
                            @if(session('duplicate_error'))
                                <p class="text-red-500 text-xs mt-1">{{ session('duplicate_error') }}</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Contact Person Field - Required -->
                            <div>
                                <x-input-label for="client_contact_person" :value="__('Contact Person')" class="text-blue-900 font-bold uppercase text-xs tracking-wider mb-2" />
                                <x-text-input id="client_contact_person" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900" type="text" name="client_contact_person" value="{{ old('client_contact_person', $client->client_contact_person) }}" required />
                                
                                @error('client_contact_person')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contact Number Field - Required -->
                            <div>
                                <x-input-label for="client_contact_number" :value="__('Contact Number')" class="text-blue-900 font-bold uppercase text-xs tracking-wider mb-2" />
                                <x-text-input id="client_contact_number" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900" type="text" name="client_contact_number" value="{{ old('client_contact_number', $client->client_contact_number) }}" required />
                                
                                @error('client_contact_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                            <a href="{{ route('clients.index') }}" class="text-gray-500 hover:text-blue-900 font-bold text-xs uppercase mr-6 transition">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg shadow-blue-500/30 transition uppercase tracking-wide text-xs">
                                {{ __('Update Client') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>