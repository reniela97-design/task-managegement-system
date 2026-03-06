<x-app-layout>
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                {{ __('Edit Type') }}
            </h2>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 p-8">
                <form method="POST" action="{{ route('types.update', $type->type_id) }}" class="max-w-xl">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="type_name" class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Type Name</label>
                        <input type="text" name="type_name" id="type_name" value="{{ old('type_name', $type->type_name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <x-input-error :messages="$errors->get('type_name')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded shadow-md transition text-xs uppercase tracking-wide">
                            Update
                        </button>
                        <a href="{{ route('types.index') }}" class="text-gray-500 hover:text-gray-700 font-bold text-xs uppercase">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>