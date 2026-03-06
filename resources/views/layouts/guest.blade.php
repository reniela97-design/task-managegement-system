<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-blue-800 via-blue-900 to-gray-900">
        <div class="mb-6">
            <x-application-logo class="w-20 h-20 fill-current text-white drop-shadow-2xl" />
        </div>
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-2xl overflow-hidden sm:rounded-lg border-t-4 border-red-900 relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-900 via-red-600 to-red-900"></div>
            {{ $slot }}
        </div>
    </div>
</body>