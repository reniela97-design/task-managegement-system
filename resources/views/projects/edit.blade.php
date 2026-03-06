<x-app-layout>
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                {{ __('Edit Project') }}
            </h2>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
                <div class="bg-blue-900 px-6 py-4 border-b border-blue-800 flex justify-between items-center">
                    <h3 class="text-white font-bold uppercase text-sm tracking-wide">
                        Editing: <span class="text-blue-200">{{ $project->project_name }}</span>
                    </h3>
                    <a href="{{ route('projects.index') }}" class="text-blue-200 hover:text-white text-xs font-bold uppercase transition">
                        &larr; Back to List
                    </a>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('projects.update', $project->project_id) }}">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-6">
                            <x-input-label for="project_name" :value="__('Project Name')" class="text-blue-900 font-bold uppercase text-xs tracking-wider mb-2" />
                            <x-text-input id="project_name" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900" type="text" name="project_name" :value="old('project_name', $project->project_name)" required autofocus />
                            <x-input-error :messages="$errors->get('project_name')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="project_client_id" :value="__('Client Owner')" class="text-blue-900 font-bold uppercase text-xs tracking-wider mb-2" />
                            <div class="relative">
                                <select name="project_client_id" id="project_client_id" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900 shadow-sm appearance-none">
                                    <option value="">-- Select Client --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->client_id }}" {{ old('project_client_id', $project->project_client_id) == $client->client_id ? 'selected' : '' }}>
                                            {{ $client->client_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <x-input-label for="project_address" :value="__('Location / Address')" class="text-blue-900 font-bold uppercase text-xs tracking-wider mb-2" />
                            <x-text-input id="project_address" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900" type="text" name="project_address" :value="old('project_address', $project->project_address)" />
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                            <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-blue-900 font-bold text-xs uppercase mr-6 transition">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg shadow-blue-500/30 transition uppercase tracking-wide text-xs">
                                {{ __('Update Project') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>