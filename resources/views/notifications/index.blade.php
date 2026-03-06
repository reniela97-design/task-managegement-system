<x-app-layout>
    {{-- HEADER --}}
    <div class="bg-white border-b border-gray-200 shadow-sm relative z-10">
        <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl text-gray-900 uppercase tracking-tight">Notification Center</h2>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-0.5">Your System Alerts & Updates</p>
                </div>
            </div>

            @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 font-bold py-2.5 px-5 rounded-xl shadow-sm transition text-[10px] uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Mark All as Read
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-3xl border border-gray-100 overflow-hidden">
                <div class="p-0">
                    @if($notifications->isEmpty())
                        <div class="p-16 text-center flex flex-col items-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center border border-gray-100 mb-5 shadow-inner">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <h3 class="text-gray-900 font-black text-xl uppercase tracking-wide">Inbox Zero</h3>
                            <p class="text-gray-500 font-medium text-sm mt-1">You have no notifications. Enjoy your day!</p>
                        </div>
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach($notifications as $notification)
                                <li class="p-6 transition-colors flex flex-col sm:flex-row items-start gap-5 {{ is_null($notification->read_at) ? 'bg-blue-50/40 hover:bg-blue-50' : 'hover:bg-gray-50/80' }}">
                                    
                                    {{-- Icon Indicator --}}
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center border shadow-sm {{ is_null($notification->read_at) ? 'bg-blue-100 text-blue-600 border-blue-200' : 'bg-gray-50 text-gray-400 border-gray-200' }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                    </div>
                                    
                                    {{-- Details --}}
                                    <div class="flex-1 w-full">
                                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-1.5 gap-2">
                                            <h4 class="text-base font-black {{ is_null($notification->read_at) ? 'text-blue-900' : 'text-gray-700' }}">
                                                {{ $notification->data['title'] ?? 'System Alert' }}
                                            </h4>
                                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 bg-white border border-gray-100 px-3 py-1 rounded-full shadow-sm whitespace-nowrap">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-sm font-medium {{ is_null($notification->read_at) ? 'text-gray-700' : 'text-gray-500' }} mb-4 leading-relaxed">
                                            {{ $notification->data['message'] }}
                                        </p>
                                        
                                        {{-- Actions --}}
                                        <div class="flex items-center gap-3">
                                            @if(isset($notification->data['task_id']))
                                                <a href="{{ route('tasks.show', $notification->data['task_id']) }}" class="text-[10px] font-black uppercase tracking-widest text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm px-4 py-2 rounded-xl flex items-center gap-1.5">
                                                    Open Workspace <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                </a>
                                            @endif
                                            
                                            @if(is_null($notification->read_at))
                                                <a href="{{ route('notifications.read', $notification->id) }}" class="text-[10px] font-bold uppercase tracking-widest text-blue-600 hover:text-blue-800 bg-white border border-blue-200 hover:border-blue-300 transition shadow-sm px-4 py-2 rounded-xl">
                                                    Mark as Read
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Unread Dot --}}
                                    @if(is_null($notification->read_at))
                                        <div class="hidden sm:flex flex-shrink-0 items-center justify-center pt-2">
                                            <div class="w-3 h-3 rounded-full bg-blue-500 shadow-sm relative">
                                                <div class="absolute inset-0 rounded-full bg-blue-400 animate-ping opacity-75"></div>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        
                        {{-- Pagination --}}
                        @if($notifications instanceof \Illuminate\Pagination\LengthAwarePaginator && $notifications->hasPages())
                            <div class="p-6 border-t border-gray-100 bg-gray-50 rounded-b-3xl">
                                {{ $notifications->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>