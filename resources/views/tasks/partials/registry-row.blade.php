@php
    $priorityText = 'Normal';
    if ($task->task_priority_id == 1) {
        $priorityText = 'High';
    } elseif ($task->task_priority_id == 3) {
        $priorityText = 'Low';
    }

    $taskData = [
        'title' => $task->task_title,
        'description' => $task->task_description ?? 'No description.',
        'priority' => $priorityText,
        'due' => $task->task_due_date ? \Carbon\Carbon::parse($task->task_due_date)->format('M d, Y') : 'No Date',
        'project' => $task->project->project_name ?? 'General',
        'client' => $task->client->client_name ?? 'Internal',
        'assignee' => $task->assignee->user_name ?? 'Unassigned',
        'status_id' => $task->task_status_id
    ];
@endphp

<tr class="hover:bg-{{ $hoverColor }}-100/40 transition cursor-pointer group" @click="modalData = {{ json_encode($taskData) }}; showModal = true;">
    
    {{-- 1. Detail (Title Only) --}}
    <td class="px-6 py-4 pl-8">
        <a href="{{ route('tasks.show', $task->task_id) }}" class="font-bold text-gray-800 text-sm group-hover:text-{{ $hoverColor }}-700 transition" @click.stop>
            {{ $task->task_title }}
        </a>
    </td>

    {{-- 2. Due Date --}}
    <td class="px-6 py-4">
        <span class="text-xs font-bold flex items-center gap-1.5 {{ $task->task_due_date && \Carbon\Carbon::parse($task->task_due_date)->isPast() && $task->task_status_id != 3 ? 'text-red-600' : 'text-gray-600' }}">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            {{ $task->task_due_date ? \Carbon\Carbon::parse($task->task_due_date)->format('M d, Y') : 'No Date Set' }}
        </span>
    </td>

    {{-- 3. Assignment --}}
    <td class="px-6 py-4">
        @if($task->assignee)
            <div class="flex items-center gap-3">
                <div class="h-7 w-7 rounded-full bg-{{ $hoverColor }}-100 text-{{ $hoverColor }}-700 flex items-center justify-center font-bold text-xs border border-{{ $hoverColor }}-200">
                    {{ substr($task->assignee->user_name, 0, 1) }}
                </div>
                <span class="text-xs font-bold text-gray-700">{{ $task->assignee->user_name }}</span>
            </div>
        @else
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Unassigned</span>
        @endif
    </td>

    {{-- 4. Priority Badges (High, Normal, Low) --}}
    <td class="px-6 py-4">
        @if($task->task_priority_id == 1)
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 border border-red-200 uppercase tracking-widest">
                🔥 High
            </span>
        @elseif($task->task_priority_id == 3)
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 uppercase tracking-widest">
                🔽 Low
            </span>
        @else
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-200 uppercase tracking-widest">
                ⏺ Normal
            </span>
        @endif
    </td>

    {{-- 5. Timeline / Dynamic --}}
    <td class="px-6 py-4">
        @php
            $statusName = $task->status->status_name ?? 'Unknown';
            $statusLower = strtolower($statusName);
            
            // Default Design (Pending / To-Do)
            $badgeColor = 'bg-blue-100 text-blue-700 border-blue-200';
            $iconPath = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
            $isSpinning = false;
            
            // Dynamic Text Matching (with ID fallback)
            if (str_contains($statusLower, 'complete') || $task->task_status_id == 3) {
                $badgeColor = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                $iconPath = 'M5 13l4 4L19 7';
            } elseif (str_contains($statusLower, 'cancel') || $task->task_status_id == 5) {
                $badgeColor = 'bg-red-100 text-red-700 border-red-200';
                $iconPath = 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636';
            } elseif (str_contains($statusLower, 'hold') || $task->task_status_id == 4) {
                $badgeColor = 'bg-amber-100 text-amber-700 border-amber-200';
                $iconPath = 'M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z';
            } elseif (str_contains($statusLower, 'progress') || $task->task_status_id == 2) {
                $badgeColor = 'bg-indigo-100 text-indigo-700 border-indigo-200';
                $iconPath = 'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z';
                $isSpinning = true;
            }
        @endphp

        <div class="flex flex-col gap-1">
            <span class="inline-flex items-center px-2 py-0.5 rounded w-max text-[10px] font-bold uppercase tracking-widest border {{ $badgeColor }}">
                <svg class="w-3 h-3 mr-1 {{ $isSpinning ? 'animate-spin' : '' }}" fill="none" viewBox="0 0 24 24" {{ !$isSpinning ? 'stroke="currentColor"' : '' }}>
                    @if($isSpinning)
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="{{ $iconPath }}"></path>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path>
                    @endif
                </svg>
                {{ $statusName }}
            </span>
            
            @if(str_contains($statusLower, 'complete'))
                <span class="text-[9px] text-gray-500 font-medium">
                    {{ $task->task_date_end ? \Carbon\Carbon::parse($task->task_date_end)->format('M d, Y') : 'Unknown Date' }}
                </span>
            @elseif(str_contains($statusLower, 'progress'))
                <span class="text-[9px] text-gray-500 font-medium">
                    Started: {{ $task->task_date_start ? \Carbon\Carbon::parse($task->task_date_start)->format('M d, Y') : '' }}
                </span>
            @endif
        </div>
    </td>

    {{-- 6. Actions --}}
    <td class="px-6 py-4 text-center">
        <div class="flex items-center justify-center gap-3">
            
            {{-- VIEW BUTTON --}}
            <a href="{{ route('tasks.show', $task->task_id) }}" class="text-gray-400 hover:text-indigo-600 transition" title="View Full Details" @click.stop>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            </a>

            {{-- EDIT & DELETE BUTTONS --}}
            @if(auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('Manager'))
                <a href="{{ route('tasks.edit', $task->task_id) }}" class="text-gray-400 hover:text-blue-600 transition" title="Edit Task" @click.stop>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </a>
                
                <form action="{{ route('tasks.destroy', $task->task_id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this task?');" @click.stop>
                    @csrf @method('DELETE')
                    <button type="submit" class="text-gray-400 hover:text-red-600 transition" title="Delete Task">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            @endif
        </div>
    </td>
</tr>