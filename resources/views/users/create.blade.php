<x-app-layout>
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                {{ __('Add New Account') }}
            </h2>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
                <div class="bg-blue-900 px-6 py-4 border-b border-blue-800 flex justify-between items-center">
                    <h3 class="text-white font-bold uppercase text-sm tracking-wide">Account Details</h3>
                    <a href="{{ route('users.index') }}" class="text-blue-200 hover:text-white text-xs font-bold uppercase transition">
                        &larr; Back to Directory
                    </a>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="mb-6">
                            <x-input-label for="user_name" :value="__('Full Name')" class="text-blue-900 font-bold uppercase text-xs tracking-wider mb-2" />
                            <x-text-input id="user_name" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900" type="text" name="user_name" :value="old('user_name')" required autofocus placeholder="e.g. Jane Doe" />
                            <x-input-error :messages="$errors->get('user_name')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="user_email" :value="__('Email Address')" class="text-blue-900 font-bold uppercase text-xs tracking-wider mb-2" />
                            <x-text-input id="user_email" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900" type="email" name="user_email" :value="old('user_email')" required placeholder="name@company.com" />
                            <x-input-error :messages="$errors->get('user_email')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <x-input-label for="user_role_id" :value="__('System Role')" class="text-blue-900 font-bold uppercase text-xs tracking-wider mb-2" />
                                <div class="relative">
                                    <select name="user_role_id" id="user_role_id" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900 shadow-sm appearance-none">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->role_id }}" {{ old('user_role_id') == $role->role_id ? 'selected' : '' }}>
                                                {{ $role->role_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('user_role_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="user_password" :value="__('Initial Password')" class="text-blue-900 font-bold uppercase text-xs tracking-wider mb-2" />
                                <x-text-input id="user_password" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900" type="password" name="user_password" required />
                                <x-input-error :messages="$errors->get('user_password')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                            <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-blue-900 font-bold text-xs uppercase mr-6 transition">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg shadow-blue-500/30 transition uppercase tracking-wide text-xs">
                                {{ __('Create Account') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>