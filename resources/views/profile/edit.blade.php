<x-app-layout>
    {{-- Custom Header matching Dashboard --}}
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="font-bold text-2xl text-blue-900 uppercase tracking-tight">
                Account Settings
            </h2>
            <div class="text-sm font-medium text-gray-500">
                User Status: <span class="text-green-600 font-bold">● ACTIVE</span>
            </div>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- 1. Profile Information Module --}}
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
                <div class="bg-blue-900 px-6 py-4 border-b border-blue-800 flex justify-between items-center">
                    <h3 class="text-white font-bold uppercase text-sm tracking-wide">Profile Information</h3>
                    <span class="bg-blue-800 text-blue-200 py-1 px-3 rounded text-xs font-bold">General</span>
                </div>
                
                <div class="p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            {{-- 2. Security/Password Module --}}
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
                <div class="bg-blue-900 px-6 py-4 border-b border-blue-800 flex justify-between items-center">
                    <h3 class="text-white font-bold uppercase text-sm tracking-wide">Security Settings</h3>
                    <span class="bg-blue-800 text-blue-200 py-1 px-3 rounded text-xs font-bold">Password</span>
                </div>
                
                <div class="p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            {{-- 3. Delete Account Module (Themed Red for "Emergency/Danger") --}}
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-red-100">
                <div class="bg-red-900 px-6 py-4 border-b border-red-800 flex justify-between items-center">
                    <h3 class="text-white font-bold uppercase text-sm tracking-wide">Danger Zone</h3>
                    <span class="bg-red-800 text-red-200 py-1 px-3 rounded text-xs font-bold">Irreversible</span>
                </div>
                
                <div class="p-8">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>