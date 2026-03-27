@php
    // Global check for pending task edits
    $pendingApprovalsCount = 0;
    if(Auth::check() && (Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Manager'))) {
        $pendingApprovalsCount = \App\Models\Task::where('task_edit_pending', true)->count();
    }

    // Global check for unread system notifications
    $unreadNotifsCount = Auth::check() ? Auth::user()->unreadNotifications->count() : 0;
@endphp

<nav x-data="{
        unreadNotifsCount: {{ $unreadNotifsCount }},
        pendingApprovalsCount: {{ $pendingApprovalsCount }},
        listenForNotifications() {
            if (typeof window.Echo !== 'undefined' && {{ Auth::check() ? 'true' : 'false' }}) {
                // Listen to the private user channel for real-time notifications
                window.Echo.private('App.Models.User.{{ Auth::id() }}')
                    .notification((notification) => {
                        this.unreadNotifsCount++;
                    });
            }
        }
    }"
    x-init="listenForNotifications()"
    class="h-full flex flex-col w-full bg-white/90 backdrop-blur-2xl border-r border-slate-200 shadow-[4px_0_24px_rgba(0,0,0,0.02)] relative">

    <button @click="toggleSidebar()" class="hidden md:flex absolute top-8 -right-3 z-50 items-center justify-center w-6 h-6 bg-white rounded-full text-black shadow-md border border-slate-200 hover:text-red-900 hover:scale-110 transition-all duration-300 focus:outline-none">
        <svg class="w-3 h-3 transition-transform duration-500" :class="sidebarExpanded ? 'rotate-180' : 'rotate-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </button>

    <div class="flex flex-col items-center pt-8 pb-6 mx-2 mb-2 transition-all duration-500">
        <a href="{{ route('dashboard') }}" class="relative flex items-center justify-center min-h-[50px]">
            <div class="absolute transition-all duration-500"
                 :class="sidebarExpanded ? 'opacity-0 pointer-events-none scale-0' : 'opacity-100 scale-100'">
                <div class="bg-white p-1.5 rounded-lg border border-slate-100 shadow-sm">
                   <svg class="w-8 h-8" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="4" y="22" width="6" height="14" rx="1" fill="#1e3a8a"/>
                        <rect x="12" y="14" width="6" height="22" rx="1" fill="#1e3a8a"/>
                        <rect x="20" y="8" width="6" height="28" rx="1" fill="#b91c1c"/>
                        <rect x="28" y="18" width="6" height="18" rx="1" fill="#1e3a8a"/>
                    </svg>
                </div>
            </div>
            <img src="{{ asset('logoo.png') }}"
                 alt="Emergence Systems"
                 class="transition-all duration-500 object-contain h-20"
                 :class="sidebarExpanded ? 'opacity-100 w-auto px-2' : 'opacity-0 w-0 overflow-hidden'"
                 onerror="this.style.display='none'"
            />
        </a>
    </div>

    <div id="sidebarScroll" class="flex-1 px-4 space-y-1 overflow-x-hidden overflow-y-auto custom-scrollbar py-2">

        <div class="px-2 mb-2 mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider transition-opacity duration-300 whitespace-nowrap"
             :class="sidebarExpanded ? 'opacity-100' : 'opacity-0 hidden'">
            Menu
        </div>

        {{-- DASHBOARD LINK WITH LIVE BLUE NOTIFICATION DOT --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center w-full px-3 py-3.5 rounded-xl transition-all duration-300 group relative
            {{ request()->routeIs('dashboard') ? 'text-red-900 bg-red-50/80 shadow-sm font-bold' : 'text-black hover:text-red-900 hover:bg-slate-50 font-medium' }}">
            @if(request()->routeIs('dashboard'))<div class="absolute left-0 top-1/2 -translate-y-1/2 h-8 w-1 bg-red-900 rounded-r-full" x-show="!sidebarExpanded"></div>@endif

            <div class="relative flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>

                {{-- Live Notification Ping --}}
                <span x-show="unreadNotifsCount > 0" x-cloak class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500 border border-white"></span>
                </span>
            </div>

            <div class="ml-3 whitespace-nowrap transition-all duration-300 flex items-center justify-between w-full" :class="sidebarExpanded ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 absolute'">
                <span>{{ __('Dashboard') }}</span>
                <span x-show="unreadNotifsCount > 0 && sidebarExpanded"
                      x-cloak
                      class="bg-blue-100 text-blue-700 text-[9px] font-bold px-1.5 py-0.5 rounded-md shadow-sm mr-2"
                      x-text="unreadNotifsCount"></span>
            </div>
        </a>

        {{-- TASKS LINK WITH RED APPROVAL DOT --}}
        <a href="{{ route('tasks.index') }}"
            class="flex items-center w-full px-3 py-3.5 rounded-xl transition-all duration-300 group relative
            {{ request()->routeIs('tasks.index') || request()->routeIs('tasks.create') || request()->routeIs('tasks.edit') ? 'text-red-900 bg-red-50/80 shadow-sm font-bold' : 'text-black hover:text-red-900 hover:bg-slate-50 font-medium' }}">
            <div class="relative flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>

                <span x-show="pendingApprovalsCount > 0" x-cloak class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 border border-white"></span>
                </span>
            </div>
            <div class="ml-3 whitespace-nowrap transition-all duration-300 flex items-center justify-between w-full" :class="sidebarExpanded ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 absolute'">
                <span>{{ __('Tasks') }}</span>
                <span x-show="pendingApprovalsCount > 0 && sidebarExpanded"
                      x-cloak
                      class="bg-red-100 text-red-700 text-[9px] font-bold px-1.5 py-0.5 rounded-md shadow-sm mr-2"
                      x-text="pendingApprovalsCount"></span>
            </div>
        </a>

        <a href="{{ route('activity.index') }}"
            class="flex items-center w-full px-3 py-3.5 rounded-xl transition-all duration-300 group relative
            {{ request()->routeIs('activity.*') ? 'text-red-900 bg-red-50/80 shadow-sm font-bold' : 'text-black hover:text-red-900 hover:bg-slate-50 font-medium' }}">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="ml-3 whitespace-nowrap transition-all duration-300" :class="sidebarExpanded ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 absolute'">{{ __('Activity') }}</span>
        </a>

        <a href="{{ route('reports.index') }}"
            class="flex items-center w-full px-3 py-3.5 rounded-xl transition-all duration-300 group relative
            {{ request()->routeIs('reports.index') ? 'text-red-900 bg-red-50/80 shadow-sm font-bold' : 'text-black hover:text-red-900 hover:bg-slate-50 font-medium' }}">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span class="ml-3 whitespace-nowrap transition-all duration-300" :class="sidebarExpanded ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 absolute'">{{ __('Reports') }}</span>
        </a>



        <a href="{{ route('reports.calendar') }}"
            class="flex items-center w-full px-3 py-3.5 rounded-xl transition-all duration-300 group relative
            {{ request()->routeIs('reports.calendar') ? 'text-red-900 bg-red-50/80 shadow-sm font-bold' : 'text-black hover:text-red-900 hover:bg-slate-50 font-medium' }}">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span class="ml-3 whitespace-nowrap transition-all duration-300" :class="sidebarExpanded ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 absolute'">{{ __('Calendar') }}</span>
        </a>



        @if(Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Manager'))
            <div class="px-2 mt-6 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider transition-opacity duration-300 whitespace-nowrap"
                 :class="sidebarExpanded ? 'opacity-100' : 'opacity-0 hidden'">
                Manage
            </div>

            {{-- REGISTRY LINK WITH RED APPROVAL DOT --}}
        <a href="{{ route('tasks.registry') }}"
            class="flex items-center w-full px-3 py-3.5 rounded-xl transition-all duration-300 group relative
            {{ request()->routeIs('tasks.registry') ? 'text-red-900 bg-red-50/80 shadow-sm font-bold' : 'text-black hover:text-red-900 hover:bg-slate-50 font-medium' }}">
            <div class="relative flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>

                <span x-show="pendingApprovalsCount > 0" x-cloak class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 border border-white"></span>
                </span>
            </div>
            <div class="ml-3 whitespace-nowrap transition-all duration-300 flex items-center justify-between w-full" :class="sidebarExpanded ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 absolute'">
                <span>{{ __('Registry') }}</span>
                <span x-show="pendingApprovalsCount > 0 && sidebarExpanded"
                      x-cloak
                      class="bg-red-100 text-red-700 text-[9px] font-bold px-1.5 py-0.5 rounded-md shadow-sm mr-2"
                      x-text="pendingApprovalsCount"></span>
            </div>
        </a>

            <a href="{{ route('gantt.index') }}"
            class="flex items-center w-full px-3 py-3.5 rounded-xl transition-all duration-300 group relative
            {{ request()->routeIs('gantt.index') ? 'text-red-900 bg-red-50/80 shadow-sm font-bold' : 'text-black hover:text-red-900 hover:bg-slate-50 font-medium dark:text-gray-200 dark:hover:text-white dark:hover:bg-slate-800' }}">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            <span class="ml-3 whitespace-nowrap transition-all duration-300" :class="sidebarExpanded ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 absolute'">{{ __('Timeline') }}</span>
        </a>

            <div x-data="{ open: {{ (request()->routeIs('clients.*') || request()->routeIs('categories.*') || request()->routeIs('systems.*') || request()->routeIs('types.*') || request()->routeIs('projects.*')) ? 'true' : 'false' }} }" class="relative">
                <button @click="open = !open; if(!sidebarExpanded) toggleSidebar();"
                    class="flex items-center w-full px-3 py-3.5 rounded-xl transition-all duration-300 group justify-between
                    {{ (request()->routeIs('clients.*') || request()->routeIs('categories.*') || request()->routeIs('systems.*') || request()->routeIs('types.*') || request()->routeIs('projects.*')) ? 'text-red-900 bg-red-50/80 shadow-sm font-bold' : 'text-black hover:text-red-900 hover:bg-slate-50 font-medium' }}">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                        <span class="ml-3 whitespace-nowrap transition-all duration-300" :class="sidebarExpanded ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 absolute'">{{ __('Standing Data') }}</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" :style="!sidebarExpanded ? 'display:none' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="mt-1 space-y-1 overflow-hidden"
                     :class="sidebarExpanded ? 'pl-9' : 'hidden'">

                    <a href="{{ route('clients.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('clients.*') ? 'text-red-900 font-bold bg-red-50' : 'text-gray-600 hover:text-red-900 hover:bg-slate-50' }}">
                        {{ __('Clients') }}
                    </a>
                    <a href="{{ route('categories.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('categories.*') ? 'text-red-900 font-bold bg-red-50' : 'text-gray-600 hover:text-red-900 hover:bg-slate-50' }}">
                        {{ __('Categories') }}
                    </a>
                    <a href="{{ route('systems.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('systems.*') ? 'text-red-900 font-bold bg-red-50' : 'text-gray-600 hover:text-red-900 hover:bg-slate-50' }}">
                        {{ __('Systems') }}
                    </a>
                    <a href="{{ route('types.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('types.*') ? 'text-red-900 font-bold bg-red-50' : 'text-gray-600 hover:text-red-900 hover:bg-slate-50' }}">
                        {{ __('Types') }}
                    </a>
                    <a href="{{ route('projects.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('projects.*') ? 'text-red-900 font-bold bg-red-50' : 'text-gray-600 hover:text-red-900 hover:bg-slate-50' }}">
                        {{ __('Projects') }}
                    </a>
                </div>
            </div>
        @endif



        @if(Auth::user()->hasRole('Administrator'))
            <div class="px-2 mt-6 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider transition-opacity duration-300 whitespace-nowrap"
                 :class="sidebarExpanded ? 'opacity-100' : 'opacity-0 hidden'">
                System
            </div>

            <a href="{{ route('users.index') }}"
                class="flex items-center w-full px-3 py-3.5 rounded-xl transition-all duration-300 group relative
                {{ request()->routeIs('users.*') ? 'text-red-900 bg-red-50/80 shadow-sm font-bold' : 'text-black hover:text-red-900 hover:bg-slate-50 font-medium' }}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span class="ml-3 whitespace-nowrap transition-all duration-300" :class="sidebarExpanded ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 absolute'">{{ __('Accounts') }}</span>
            </a>

            <a href="{{ route('roles.index') }}"
                class="flex items-center w-full px-3 py-3.5 rounded-xl transition-all duration-300 group relative
                {{ request()->routeIs('roles.*') ? 'text-red-900 bg-red-50/80 shadow-sm font-bold' : 'text-black hover:text-red-900 hover:bg-slate-50 font-medium' }}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span class="ml-3 whitespace-nowrap transition-all duration-300" :class="sidebarExpanded ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 absolute'">{{ __('Roles') }}</span>
            </a>
        @endif
    </div>

    <div class="p-4 mx-2 mb-4 mt-auto rounded-2xl bg-slate-50 border border-slate-200 transition-all duration-300"
         :class="sidebarExpanded ? '' : 'bg-transparent border-none'">

        <div class="flex items-center gap-3" :class="sidebarExpanded ? 'mb-4' : 'justify-center mb-2'">
            <div class="relative">
                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-slate-200 shadow-inner flex items-center justify-center text-slate-700 font-bold border-2 border-white">
                    {{ substr(Auth::user()->user_name, 0, 1) }}
                </div>
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
            </div>
            <div class="overflow-hidden transition-all duration-300" :class="sidebarExpanded ? 'opacity-100 w-auto' : 'opacity-0 w-0 hidden'">
                <div class="font-bold text-black text-sm truncate">{{ Auth::user()->user_name }}</div>
                <div class="text-xs text-gray-500 truncate">{{ Auth::user()->user_email }}</div>
            </div>
        </div>

        <div class="grid gap-2 transition-all duration-300"
             :class="sidebarExpanded ? 'grid-cols-2 opacity-100' : 'grid-cols-1 opacity-100'">

            <a href="{{ route('profile.edit') }}"
               class="flex items-center justify-center p-2 rounded-lg text-black hover:bg-white hover:text-red-900 hover:shadow-sm transition-all"
               x-bind:title="!sidebarExpanded ? 'Settings' : ''">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
               <span class="ml-2 text-xs font-bold" :class="sidebarExpanded ? 'block' : 'hidden'">Settings</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center p-2 rounded-lg text-black hover:bg-white hover:text-red-900 hover:shadow-sm transition-all"
                        x-bind:title="!sidebarExpanded ? 'Log Out' : ''">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span class="ml-2 text-xs font-bold" :class="sidebarExpanded ? 'block' : 'hidden'">Log Out</span>
                </button>
            </form>
        </div>
    </div>
</nav>
