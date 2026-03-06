<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Status') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('statuses.store') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <x-input-label for="status_name" :value="__('Status Name')" />
                        <x-text-input id="status_name" class="block mt-1 w-full" type="text" name="status_name" required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="status_color" :value="__('Color (Hex Code)')" />
                        <x-text-input id="status_color" class="block mt-1 w-full" type="color" name="status_color" value="#3b82f6" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('statuses.index') }}" class="text-gray-600 dark:text-gray-400 hover:underline mr-4">Cancel</a>
                        <x-primary-button>{{ __('Save Status') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>