<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
    // This script runs INSTANTLY before the body loads
    if (localStorage.getItem('sidebarExpanded') === 'false') {
        document.documentElement.classList.add('sidebar-collapsed');
    }
</script>

        <style>
            /* Smooth scrolling and custom scrollbar */
            html { scroll-behavior: smooth; }
            ::-webkit-scrollbar { width: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
            [x-cloak] { display: none !important; }

            /* Anti-jitter safety: forces width to match Tailwind's md:w-24 before Alpine loads */
            html.sidebar-collapsed aside { width: 6rem !important; }

            /* resources/views/layouts/app.blade.php */

/* 1. Force the sidebar width immediately */
html.sidebar-collapsed aside {
    width: 6rem !important;
}

/* 2. Hide all nav text spans immediately when collapsed */
html.sidebar-collapsed aside span,
html.sidebar-collapsed aside .ml-3 {
    display: none !important;
    opacity: 0 !important;
}

/* 3. Hide the logo and menu headers immediately */
html.sidebar-collapsed aside img,
html.sidebar-collapsed aside .uppercase {
    display: none !important;
}
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900
bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))]
from-slate-100 via-slate-50 to-blue-50/30">

        <div x-data="{
        sidebarExpanded: localStorage.getItem('sidebarExpanded') === null ? true : localStorage.getItem('sidebarExpanded') === 'true',
        mobileOpen: false,
        toggleSidebar() {
            this.sidebarExpanded = !this.sidebarExpanded;
            localStorage.setItem('sidebarExpanded', this.sidebarExpanded);

            // This ensures the CSS classes stay in sync with the state
            if (this.sidebarExpanded) {
                document.documentElement.classList.remove('sidebar-collapsed');
            } else {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        }
    }"
    class="min-h-screen flex flex-col md:flex-row overflow-hidden relative">

            <aside :class="sidebarExpanded ? 'md:w-72' : 'md:w-24'"
                   class="hidden md:flex flex-col flex-shrink-0 z-50 h-screen sticky top-0 transition-all duration-500 ease-in-out">
                @include('layouts.navigation')
            </aside>

            <div x-show="mobileOpen" x-cloak class="fixed inset-0 z-50 flex md:hidden">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="mobileOpen = false"></div>
                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white shadow-2xl h-full">
                    @include('layouts.navigation')
                </div>
            </div>

            <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden relative">

                @isset($header)
                    <header class="sticky top-0 z-40 px-8 py-4 w-full">
                        <div class="bg-white/70 backdrop-blur-xl border border-white/50 shadow-sm rounded-2xl px-6 py-3 flex justify-between items-center">
                            <div class="flex items-center gap-4 md:hidden">
                                <button @click="mobileOpen = true" class="text-slate-500 hover:text-slate-800">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                </button>
                            </div>

                            <div class="hidden md:flex items-center bg-slate-100/50 rounded-full px-4 py-2 border border-transparent focus-within:border-blue-300 focus-within:bg-white transition-all w-96">
                                <svg class="w-5 h-5 text-slate-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></path></svg>
                                <input type="text" placeholder="Search tasks, projects..." class="bg-transparent border-none focus:ring-0 text-sm text-slate-700 placeholder-slate-400 w-full p-0">
                            </div>

                            <div class="flex items-center gap-4">
                                {{ $header }}
                            </div>
                        </div>
                    </header>
                @endisset

                <main class="flex-1 overflow-y-auto p-4 md:p-8 pt-2 custom-scrollbar">
                    {{ $slot }}
                </main>
            </div>
        </div>
        <script>
document.addEventListener("DOMContentLoaded", function () {

    const sidebar = document.getElementById("sidebarScroll");

    if (!sidebar) return;

    // Restore scroll
    const savedScroll = localStorage.getItem("sidebarScroll");
    if (savedScroll !== null) {
        sidebar.scrollTop = savedScroll;
    }

    // Save scroll before leaving page
    sidebar.addEventListener("scroll", function () {
        localStorage.setItem("sidebarScroll", sidebar.scrollTop);
    });

});
</script>
    </body>
</html>
