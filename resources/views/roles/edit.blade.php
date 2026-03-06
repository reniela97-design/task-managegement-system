<x-app-layout>
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                {{ __('Edit Role') }}
            </h2>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
                <div class="bg-blue-900 px-6 py-4 border-b border-blue-800 flex justify-between items-center">
                    <h3 class="text-white font-bold uppercase text-sm tracking-wide">
                        Editing: <span class="text-blue-200">{{ $role->role_name }}</span>
                    </h3>
                    <a href="{{ route('roles.index') }}" class="text-blue-200 hover:text-white text-xs font-bold uppercase transition">
                        &larr; Back to List
                    </a>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('roles.update', $role->role_id) }}">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-6">
                            <x-input-label for="role_name" :value="__('Role Name')" class="text-blue-900 font-bold uppercase text-xs tracking-wider mb-2" />
                            <x-text-input id="role_name" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900" type="text" name="role_name" :value="old('role_name', $role->role_name)" required />
                            <x-input-error :messages="$errors->get('role_name')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                            <a href="{{ route('roles.index') }}" class="text-gray-500 hover:text-blue-900 font-bold text-xs uppercase mr-6 transition">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg shadow-blue-500/30 transition uppercase tracking-wide text-xs">
                                {{ __('Update Role') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>