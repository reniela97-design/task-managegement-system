<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit System
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('systems.update', $system->system_id) }}">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-4">
                        <x-input-label for="system_name" :value="__('System Name')" />
                        <x-text-input id="system_name" class="block mt-1 w-full" type="text" name="system_name" :value="old('system_name', $system->system_name)" required />
                        
                        <!-- Error message for empty field (S2.2) -->
                        @error('system_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        
                        <!-- Success message for update (S2.3) -->
                        @if(session('success'))
                            <p class="text-green-500 text-xs mt-1 font-medium">✓ {{ session('success') }}</p>
                        @endif
                        
                        <!-- Error message for duplicate system -->
                        @if(session('error'))
                            <p class="text-red-500 text-xs mt-1">{{ session('error') }}</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('systems.index') }}" class="text-gray-600 dark:text-gray-400 hover:underline mr-4">{{ __('Cancel') }}</a>
                        <x-primary-button>{{ __('Update System') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>