<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Category
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('categories.update', $category->category_id) }}">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-4">
                        <x-input-label for="category_name" :value="__('Category Name')" />
                        <x-text-input id="category_name" class="block mt-1 w-full" type="text" name="category_name" :value="old('category_name', $category->category_name)" required />
                        
                        <!-- Error message for empty field (U2.2) -->
                        @error('category_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        
                        <!-- Success message for update (U2.3) -->
                        @if(session('success'))
                            <p class="text-green-500 text-xs mt-1 font-medium">✓ {{ session('success') }}</p>
                        @endif
                        
                        <!-- Error message for duplicate category -->
                        @if(session('error'))
                            <p class="text-red-500 text-xs mt-1">{{ session('error') }}</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('categories.index') }}" class="text-gray-600 dark:text-gray-400 hover:underline mr-4">{{ __('Cancel') }}</a>
                        <x-primary-button>{{ __('Update Category') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>